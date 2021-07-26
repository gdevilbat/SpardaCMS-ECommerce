<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Gdevilbat\SpardaCMS\Modules\Core\Http\Controllers\CoreController;
use Gdevilbat\SpardaCMS\Modules\Core\Entities\Setting;
use Illuminate\Http\Exceptions\HttpResponseException;

use Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\Product;
use Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta;

use Log;
use DB;
use MarketPlace;

class ShopeeController extends CoreController
{
    public function authentication(Request $request)
    {
        return MarketPlace::driver('shopee')->shop->getAuthUrl(url(action('\\'.Self::class.'@callback').'?'.http_build_query(['callback' => $request->input('callback')])));
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
        $this->validate($request, [
            'shop_id' => 'required'
        ]);
        
        $column = [Product::getPrimaryKey(), 'post_title', '', ''];

        $length = !empty($request->input('length')) ? $request->input('length') : 10 ;
        $column = !empty($request->input('order.0.column')) ? $column[$request->input('order.0.column')] : Product::getPrimaryKey() ;
        $dir = !empty($request->input('order.0.dir')) ? $request->input('order.0.dir') : 'DESC' ;
        $searchValue = $request->input('search')['value'];

        $query = Product::with(['postMeta', 'productMeta'])
                        ->whereHas('postMeta', function($query) use ($request){
                            $query->where('meta_key', ProductMeta::SHOPEE_STORE)
                                  ->whereRaw("meta_value REGEXP('shop_id(.{2,3})".$request->shop_id."(.{0,1})\,')"); 
                        })
                        ->whereHas('productMeta', function($query){
                            $query->where('product_stock', '>', 0);
                        })
                        ->orderBy($column, $dir)
                        ->limit($length);

        $recordsTotal = $query->count();
        $filtered = $query;

        if($searchValue)
        {
            $filtered->where(function($query) use ($searchValue){
                $query->where(DB::raw("CONCAT(post_title)"), 'like', '%'.$searchValue.'%');
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
                $shopee_store = $post->meta->getMetaData(ProductMeta::SHOPEE_STORE);

                $data[$i][] = $post->getKey();
                $data[$i][] = '<a class="item-promotion" href="javascript:void(0)" data-status="'.$post->productMeta->availability.'" data-name="'.$post->post_title.'" data-merchant="'.$shopee_store->shop_id.'" data-product="'.$shopee_store->product_id.'" data-id="'.$post->getKey().'">'.$post->post_title.'</a>';

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

    public function marketplace()
    {
        return view('ecommerce::admin.'.$this->data['theme_cms']->value.'.content.Shopee.marketplace', $this->data);        
    }

    public function scheduleItem()
    {
        $items = [];

        if(!empty(getSettingConfig('shopee_item_scheduled')))
        {
            $posts = Product::with('productMeta')->whereIn(Product::getPrimaryKey(), getSettingConfig('shopee_item_scheduled'))->get();
            foreach ($posts as $key => $post) {
                $items[$key]['name'] = $post->post_title;
                $items[$key][Product::FOREIGN_KEY] = $post->getKey();
                $items[$key]['status'] = $post->productMeta->availability;
            }
        }

        return response()->json($items);
    }

    public function saveItemScheduled(Request $request)
    {
        $config = Setting::where('name', 'shopee_item_scheduled')->first();
        if(empty($config))
            $config = new Setting;

        $config->name = 'shopee_item_scheduled';
        $config->value = $request->input('items');
        $config->save();

        return$this->publishItemPromotion($request);
    }

    public function publishItemPromotion(Request $request)
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
                    $shopee_store = $post->meta->getMetaData(ProductMeta::SHOPEE_STORE);
                    array_push($item_id, (int) $shopee_store->product_id);
                }

                $request->merge([
                    'shop_id' => (int) $shopee_id, 
                    'item_id' => $item_id]
                );

                MarketPlace::driver('shopee')->item->setBoostedItem($request->input());
            }
        }

        return redirect()->back()->with('global_message',['status' => 200, 'message' => 'Success To Update Setting']);
    }

    public function getDiscountsList(Request $request)
    {
        $this->validate($request, [
            'page' => 'required',
        ]);

        $per_page = 10;

        $request->merge([
            'discount_status' => 'ALL',
            'pagination_offset' => ($request->page - 1) * $per_page, 
            'pagination_entries_per_page' => $per_page
        ]);

        $data = Marketplace::driver('shopee')->discount->getDiscountsList($request->input());

        return response()->json($data);
    }

    public function getDiscountDetail(Request $request)
    {
        $this->validate($request, [
            'page' => 'required',
        ]);

        $per_page = 100;

        $request->merge([
            'pagination_offset' => ($request->page - 1) * $per_page, 
            'pagination_entries_per_page' => $per_page
        ]);

        $discount_item = Marketplace::driver('shopee')->discount->getDiscountDetail($request->input());

        $items = $discount_item->items;

        $arr_id = [];

        foreach ($items as $key => $value) {
            $arr_id[$key] = $value->item_id;
        }

        $exclude_id = implode('|', $arr_id);

        $query = Product::with(['postMeta', 'productMeta'])
                        ->where(function($query) use ($request, $exclude_id){
                            $query->where(function($query) use ($request, $exclude_id) {
                                    $query->where(function($query) use ($request, $exclude_id){
                                        $query->whereHas('postMeta', function($query) use ($request, $exclude_id){
                                                $query->where(function($query) use ($request){
                                                            $query->where('meta_key', ProductMeta::SHOPEE_STORE)
                                                              ->whereRaw("meta_value REGEXP('shop_id(.{2,3})".$request->shop_id."(.{0,1})\,')"); 
                                                        })
                                                      ->where(function($query) use ($exclude_id){
                                                            if($exclude_id != '')
                                                            {
                                                                $query->where('meta_key', ProductMeta::SHOPEE_STORE)
                                                                  ->whereRaw("meta_value NOT REGEXP('product_id(.{2,3})(".$exclude_id.")')");
                                                              }
                                                      });
                                            })
                                            ->whereHas('postMeta', function($query) use ($request, $exclude_id){
                                                $query->where(function($query){
                                                        $query->where('meta_key', ProductMeta::PRODUCT_VARIANT)
                                                              ->whereRaw("meta_value REGEXP('sale(.{3})[^0]')");
                                                    });
                                            });
                                    });
                            })->orWhere(function($query) use ($request, $exclude_id){
                                $query->whereHas('productMeta', function($query){
                                        $query->where('product_sale', '>', 0);
                                    })
                                     ->whereHas('postMeta', function($query) use ($request, $exclude_id){
                                                $query->where(function($query) use ($request){
                                                            $query->where('meta_key', ProductMeta::SHOPEE_STORE)
                                                              ->whereRaw("meta_value REGEXP('shop_id(.{2,3})".$request->shop_id."(.{0,1})\,')"); 
                                                        })
                                                      ->where(function($query) use ($exclude_id){
                                                            if($exclude_id != '')
                                                            {
                                                                $query->where('meta_key', ProductMeta::SHOPEE_STORE)
                                                                  ->whereRaw("meta_value NOT REGEXP('product_id(.{2,3})(".$exclude_id.")')");
                                                              }
                                                      });
                                            });
                            });
                        })
                        ->whereHas('productMeta', function($query){
                            $query->where('product_stock', '>', 0);
                        });

        $available_items = $query->get();

        $data = [
            'discount' => $discount_item,
            'available_items' => $available_items
        ];

        return response()->json($data);
    }

    public function addDiscountItem(Request $request)
    {
        $this->validate($request, [
            'post_id' => 'required'
        ]);

        $post = Product::with('productMeta')->findOrFail($request->post_id);

        $shopee = $post->meta->getMetaData(ProductMeta::SHOPEE_STORE);

        $data = [];

        $data['discount_id'] = $request->discount_id;

        $data['items'][0]['item_id'] = $shopee->product_id;
        $data['items'][0]['purchase_limit'] = 0;

        $variant = $post->meta->getMetaData(ProductMeta::PRODUCT_VARIANT);

        if(!empty($variant))
        {
            $childrens = $variant;
            $shopee_childrens = $shopee->children;

            $i = 0;
            foreach ($childrens as $key => $children) {
                if((int) $children->sale > 0)
                {
                    if(array_key_exists($key, $shopee_childrens))
                    {
                        $data['items'][0]['variations'][$i]['variation_id'] = $shopee_childrens[$key]->product_id;
                        $data['items'][0]['variations'][$i]['variation_promotion_price'] = $children->sale;
                        $i++;
                    }
                }
            }
        }


        if($post->productMeta->product_sale > 0)
        {
            $data['items'][0]['variation_promotion_price'] = $post->productMeta->product_sale;
        }

        $request->merge($data);
        
        $data = $request->input();

        $data = Marketplace::driver('shopee')->discount->addDiscountItems($request->input());

        if(!property_exists($data, 'discount_id')){
            throw new HttpResponseException(response()->json([
                'errors'  => [
                    'msg' => [
                        $data->msg
                    ]
                ]
            ], 422));
        }

        return response()->json($data);
    }
}
