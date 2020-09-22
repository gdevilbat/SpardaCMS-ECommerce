<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Gdevilbat\SpardaCMS\Modules\Core\Http\Controllers\CoreController;
use Gdevilbat\SpardaCMS\Modules\Core\Entities\Setting;

use Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\Product;

use Log;
use DB;

class ShopeeController extends CoreController
{
    public function authentication(Request $request)
    {
        $time = \Carbon\Carbon::now()->timestamp;
        $path = '/api/v2/shop/auth_partner';
        $base_string =  config('cms-ecommerce.SHOPEE_PARTNER_ID').$path.$time;
        $sign = $this->getSignature($base_string);
        $url = url(config('cms-ecommerce.SHOPEE_API_URL').$path.'?'.http_build_query(['partner_id' => config('cms-ecommerce.SHOPEE_PARTNER_ID'), 'redirect' => url(action('\\'.Self::class.'@callback').'?'.http_build_query(['callback' => $request->input('callback')])), 'timestamp' => $time, 'sign' => $sign]));
        return redirect($url);
    }

    public function callback(Request $request)
    {
        $config = Setting::where('name', 'shopee_id')->first();
        if(empty($config))
            $config = new Setting;

        $config->name = 'shopee_id';
        $config->value = $request->input('shop_id');
        $config->save();

        if($request->has('callback'))
            return call_user_func_array(array($this, $request->input('callback')), []);

        return '<script>if (window.opener){window.close()}</script>';
    }

    private function refreshAndClose()
    {
       return '<script>
                   if (window.opener){
                    window.opener.location.reload();
                    window.close();
                }
               </script>';
    }

    public function serviceMaster(Request $request)
    {
        $column = [Product::getPrimaryKey(), 'post_title', '', ''];

        $length = !empty($request->input('length')) ? $request->input('length') : 10 ;
        $column = !empty($request->input('order.0.column')) ? $column[$request->input('order.0.column')] : Product::getPrimaryKey() ;
        $dir = !empty($request->input('order.0.dir')) ? $request->input('order.0.dir') : 'DESC' ;
        $searchValue = $request->input('search')['value'];

        $query = Product::with(['postMeta', 'productMeta'])
                        ->whereHas('postMeta', function($query){
                            $query->where('meta_key', 'shopee_slug')
                                  ->where('meta_value', 'LIKE', '%'.addslashes('product\/'.getSettingConfig('shopee_id')).'%'); 
                        })
                        ->whereHas('productMeta', function($query){
                            $query->where('availability', Product::STAT_INSTOCK);
                        })
                        ->orderBy($column, $dir)
                        ->limit($length);

        $recordsTotal = $query->count();
        $filtered = $query;

        if($searchValue)
        {
            $filtered->where(function($query) use ($searchValue){
                $query->where(DB::raw("CONCAT(post_title)"), 'like', '%'.$searchValue.'%')
                      ->orWhereHas('productMeta',function($query) use ($searchValue){
                        //$query->where(DB::raw("CONCAT(availability)"), 'like', '%'.$searchValue.'%');
                      });
            });
        }

        $filteredTotal = $filtered->count();

        $this->data['length'] = $length;
        $this->data['column'] = $column;
        $this->data['dir'] = $dir;
        $this->data['posts'] = $filtered->offset($request->input('start'))->limit($length)->get();

        /*=========================================
        =            Parsing Datatable            =
        =========================================*/
            
            $data = array();
            $i = 0;
            foreach ($this->data['posts'] as $key => $post) 
            {
                $data[$i][] = $post->getKey();
                $data[$i][] = '<a class="item-promotion" href="javascript:void(0)" data-name="'.$post->post_title.'" data-shopee-url="'.$post->postMeta->where('meta_key', 'shopee_slug')->first()->meta_value.'" data-id="'.$post->getKey().'">'.$post->post_title.'</a>';

                if(!empty($post->productMeta->product_sale) && $post->productMeta->product_sale < $post->productMeta->product_price)
                {
                    $data[$i][] = $post->productMeta->product_sale;
                }
                else
                {
                    $data[$i][] = $post->productMeta->product_price;
                }

                $data[$i][] = $post->productMeta->availability;
                $i++;
            }
        
        /*=====  End of Parsing Datatable  ======*/
        
        return ['data' => $data, 'draw' => (integer)$request->input('draw'), 'recordsTotal' => $recordsTotal, 'recordsFiltered' => $filteredTotal];
    }

    public function shopeePromotion()
    {
        $items = [];

        if(!empty(getSettingConfig('shopee_item_scheduled')))
        {
            $posts = Product::whereIn(Product::getPrimaryKey(), getSettingConfig('shopee_item_scheduled'))->get();
            foreach ($posts as $key => $post) {
                $items[$key]['name'] = $post->post_title;
                $items[$key]['post_id'] = $post->getKey();
            }
        }


        $this->data['items'] = $items;

        return view('ecommerce::admin.'.$this->data['theme_cms']->value.'.content.Shopee.master', $this->data);        
    }

    public function saveItemScheduled(Request $request)
    {
        $config = Setting::where('name', 'shopee_item_scheduled')->first();
        if(empty($config))
            $config = new Setting;

        $config->name = 'shopee_item_scheduled';
        $config->value = $request->input('items');
        $config->save();

        return$this->publishItemPromotion();
    }

    public function publishItemPromotion()
    {
        if(empty(getSettingConfig('shopee_id')))
            return redirect()->back()->with('global_message',['status' => 500, 'message' => 'Gagal Boost Item, Authentication First !!!']);

        $shopee_id = getSettingConfig('shopee_id');

        if(!empty(getSettingConfig('shopee_item_scheduled')))
        {
            $item_id = [];
            $posts = Product::with('postMeta')
                            ->whereIn(Product::getPrimaryKey(), getSettingConfig('shopee_item_scheduled'))->get();

            if($posts->count() > 0)
            {
                foreach ($posts as $key => $post) {
                    $url = $post->postMeta->where('meta_key', 'shopee_slug')->first()->meta_value;
                    $part = explode( '/', $url);
                    $id = (int) $part[2];
                    array_push($item_id, $id);
                }

                (new \Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\Shopee\ShopeeRepository)->item->SetBoostedItem(['shop_id' => (int) $shopee_id, 'item_id' => $item_id]);
            }
        }

        return redirect()->back()->with('global_message',['status' => 200, 'message' => 'Success To Update Setting']);
    }

    protected final function getSignature($base_string)
    {
      return hash_hmac('SHA256', $base_string, config('cms-ecommerce.SHOPEE_PARTNER_SECRET'));
    }
}
