<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Micro;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\Product;
use Gdevilbat\SpardaCMS\Modules\Post\Entities\PostMeta;

use Log;

class ShopeeController
{
    protected $shopeeRepository;

    public function __construct()
    {
        $this->shopeeRepository = new \Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\Shopee\ShopeeRepository;
    }

    public function shopGetDetail(Request $request)
    {
        return $this->shopeeRepository->shop->getShopDetail($request->all());
    }

    public function itemUpload(Request $request)
    {
        return $this->shopeeRepository->item->Upload($request->all());
    }

    public function itemGetList(Request $request)
    {
        return $this->shopeeRepository->item->getItemsList($request->all());
    }

    public function itemGetDetail(Request $request)
    {
        return $this->shopeeRepository->item->getItemDetail($request->all());
    }

    public function itemUpdate(Request $request)
    {
        return $this->shopeeRepository->item->itemUpdate($request->all());
    }

    public function itemGetBoosted(Request $request)
    {
        return $this->shopeeRepository->item->getBoostedItem($request->all());
    }

    public function itemAdd(Request $request)
    {
        $request->validate([
            'id_posts' => 'required',
            'days_to_ship' => 'numeric|between:7,30',
        ]);

        $data['shop_id'] = $request->input('shop_id');
        $data['name'] = $request->input('product_name');
        $data['description'] = $request->input('product_description');
        $data['stock'] = (integer) $request->input('product_stock');
        $data['price'] = (float) $request->input('product_price');
        $data['weight'] = (float) $request->input('product_weight');
        $data['category_id'] = (integer) $request->input('category_id');

        if($request->has('is_pre_order'))
        {
            $data['days_to_ship'] = (integer) $request->input('days_to_ship');
            $data['is_pre_order'] = (boolean) $request->input('is_pre_order');
        }

        /*=========================================
        =            Parsing Attribute            =
        =========================================*/
        
            $tmp = collect($request->input('product_attributes'));

            $filtetered_attributes =  $tmp->filter(function ($attribute, $key) {
                return !empty($attribute['value']);
            });

            $attributes = $filtetered_attributes->map(function ($attribute, $key) {
                $attribute['attributes_id'] = (integer) $attribute['attributes_id'];
                return $attribute;
            })->toArray();

            $attributes = array_values($attributes);

            $data['attributes'] = $attributes;
        
        /*=====  End of Parsing Attribute  ======*/
        
        /*=====================================
        =            Parsing Image            =
        =====================================*/
        
            $images_data = (new \Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\Shopee\ShopeeRepository)->image->uploadImage($request->only(['shop_id', 'product_image']));
            $images_data = json_decode($images_data->getContent());

            if(!property_exists($images_data, 'images')){
                return response()->json([
                    'errors' => [
                        'msg' => [$images_data->msg]
                    ]
                ] ,422);
            }

            foreach ($images_data->images as $image) {
                $data['images'][]['url'] = $image->shopee_image_url;    
            }
        
        /*=====  End of Parsing Image  ======*/

        /*=========================================
        =            Parsing logistics            =
        =========================================*/
        
            $tmp = collect($request->input('product_logistic'));

            $logistics = $tmp->map(function($logistic, $key){
                $logistic['logistic_id'] = (integer) $logistic['logistic_id'];
                $logistic['enabled'] = (boolean) $logistic['enabled'];

                return $logistic;
            });

            $data['logistics'] = $logistics->toArray();
        
        /*=====  End of Parsing logistics  ======*/

        $response = (new \Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\Shopee\ShopeeRepository)->item->addItem($data);
        $response = json_decode($response->getContent());

        if(!property_exists($response, 'item')){
            return response()->json([
                'errors' => [
                    'msg' => [$response->msg]
                ]
            ] ,422);
        }

        $slug = 'product/'.$response->item->shopid.'/'.$response->item->item_id;

        PostMeta::unguard();

        PostMeta::updateOrCreate(
            ['meta_key' => 'shopee_slug', 'post_id' => decrypt($request->id_posts)],
            ['meta_value' => $slug]
        );

        PostMeta::reguard();

        return response()->json([
                'status' => 'Success'
            ] ); 
    }
}
