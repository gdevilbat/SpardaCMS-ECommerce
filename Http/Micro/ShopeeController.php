<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Micro;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\Product;
use Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta;
use Gdevilbat\SpardaCMS\Modules\Post\Entities\PostMeta;

use Log;

use Validator;

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
        Validator::validate($request->input(), [
         'product_id' => 'required',
         'shop_id' => 'required',
          Product::FOREIGN_KEY => 'required'
        ]);

        $post = Product::with('productMeta')
                        ->findOrfail($request->input(Product::FOREIGN_KEY));

        $response = [];

        /*========================================
        =            Update Item Info            =
        ========================================*/

            $parameter = [];
        
            $parameter['shop_id'] = $request->input('shop_id');
            $parameter['item_id'] = (int) $request->input('product_id');
            $parameter['name'] = $post->post_title;
            $parameter['description'] = html_entity_decode(strip_tags($post->post_content));

            $data = $this->shopeeRepository->item->itemUpdate($parameter);

            $data_item = json_decode($data->getContent());

            array_push($response, $data_item);
        
        /*=====  End of Update Item Info  ======*/

        $product_variant = $post->meta->getMetaData(ProductMeta::PRODUCT_VARIANT);

        if(empty($product_variant))
        {
            /*====================================
            =            Update Price            =
            ====================================*/

                if($post->productMeta->product_sale > 0 && $post->productMeta->product_sale < $post->productMeta->product_price)
                {
                    $price = $post->productMeta->product_sale;
                }
                else
                {
                    $price = $post->productMeta->product_price;
                }

                $parameter = [];
            
                $parameter['shop_id'] = $request->input('shop_id');
                $parameter['item_id'] = (int) $request->input('product_id');
                $parameter['price'] = (int )$price;

                $data = $this->shopeeRepository->item->itemUpdatePrice($parameter);

                $data = json_decode($data->getContent());

                array_push($response, $data);
            
            /*=====  End of Update Price  ======*/

            /*=============================================
            =            Section comment block            =
            =============================================*/

                $availability = [Product::STAT_INSTOCK, Product::STAT_PREORDER];

                if (in_array( $post->productMeta->availability, $availability)) 
                {
                    $stock = rand(5,9);
                }
                else
                {
                    $stock = 0;
                }
            
                $parameter = [];
            
                $parameter['shop_id'] = $request->input('shop_id');
                $parameter['item_id'] = (int) $request->input('product_id');
                $parameter['stock'] = (int ) $stock;

                $data = $this->shopeeRepository->item->itemUpdateStock($parameter);

                $data = json_decode($data->getContent());

                array_push($response, $data);
            
            /*=====  End of Section comment block  ======*/

            if(!empty($data_item->item->variations))
            {
                $variant = [];

                foreach ($data_item->item->variations as $key => $value) {
                    $variant[$key] = ['product_id' => $value->variation_id];
                }

                $value = ['shop_id' => $data_item->item->shopid, 'product_id' => $data_item->item->item_id, 'is_variant' =>  true, 'children' => $variant];

                PostMeta::unguard();

                PostMeta::updateOrCreate(
                    ['meta_key' => ProductMeta::SHOPEE_STORE, Product::FOREIGN_KEY => $request->input(Product::FOREIGN_KEY)],
                    ['meta_value' => $value]
                );

                PostMeta::reguard();
            }
        }
        else
        {
            $product_variant = $product_variant->get();

            $tiers = [];

            foreach ($product_variant->variants as $key_variant => $variant) {
                $tiers[$key_variant]['name'] = $variant->name;
                foreach ($variant->option as $key_option => $option) {
                    $tiers[$key_variant]['options'][$key_option] = $option->value;
                }
            };

            $variations = [];

            foreach ($product_variant->children as $key_child => $child) {
                $tmp = explode(",", $child->tier_index);

                foreach ($tmp as $key => $value) {
                    $variations[$key_child]['tier_index'][$key] = (integer) $value;
                }

                $variations[$key_child]['price'] = (integer) $child->price;
                $variations[$key_child]['stock'] = (integer) $child->stock->stock;
            }

            $parameter = [];

            $parameter['shop_id'] = $request->input('shop_id');
            $parameter['item_id'] = (int) $request->input('product_id');
            $parameter['tier_variation'] = $tiers;
            $parameter['variation'] = $variations;
            $parameter[Product::FOREIGN_KEY] = $request->input(Product::FOREIGN_KEY);

            $data = $this->addVariation($parameter);

            $data = json_decode($data->getContent());

            array_push($response, $data);

        }

        return response()->json($response);
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
        $data['condition'] = strtoupper($request->input('condition'));

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

        $value = ['shop_id' => $response->item->shopid, 'product_id' => $response->item->item_id, 'is_variant' =>  false];

        PostMeta::unguard();

        PostMeta::updateOrCreate(
            ['meta_key' => ProductMeta::SHOPEE_STORE, Product::FOREIGN_KEY => decrypt($request->id_posts)],
            ['meta_value' => $value]
        );

        PostMeta::reguard();

        if($request->has('meta.product_variant'))
        {
            $tiers = [];

            foreach ($request->input('meta.product_variant.variants') as $key_variant => $variant) {
                $tiers[$key_variant]['name'] = $variant['name'];
                foreach ($variant['option'] as $key_option => $option) {
                    $tiers[$key_variant]['options'][$key_option] = $option['value'];
                }
            };

            $variations = [];

            foreach ($request->input('meta.product_variant.children') as $key_child => $child) {
                $tmp = explode(",", $child['tier_index']);

                foreach ($tmp as $key => $value) {
                    $variations[$key_child]['tier_index'][$key] = (integer) $value;
                }

                $variations[$key_child]['price'] = (integer) $child['price'];
                $variations[$key_child]['stock'] = (integer) $child['stock']['stock'];
            }

            $data['shop_id'] = $request->input('shop_id');
            $data['item_id'] = $response->item->item_id;
            $data['tier_variation'] = $tiers;
            $data['variation'] = $variations;
            $data[Product::FOREIGN_KEY] = decrypt($request->input(Product::getPrimaryKey()));

            $this->addVariation($data);
        }

        return response()->json([
                'status' => 'Success'
            ] ); 
    }

    public function addVariation(array $data)
    {
        Validator::validate($data, [
            Product::FOREIGN_KEY => 'required',
        ]);

        $response = (new \Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\Shopee\ShopeeRepository)->item->itemInitTierVariations($data);
        $response_variant = json_decode($response->getContent());

        if(property_exists($response_variant, 'variation_id_list'))
        {
            $variant = [];

            foreach ($response_variant->variation_id_list as $key => $value) {
                $variant[$key] = ['product_id' => $value->variation_id];
            }

            $value = ['shop_id' => $data['shop_id'], 'product_id' => $response_variant->item_id, 'is_variant' =>  true, 'children' => $variant];

            PostMeta::unguard();

            PostMeta::updateOrCreate(
                ['meta_key' => ProductMeta::SHOPEE_STORE, Product::FOREIGN_KEY => $data[Product::FOREIGN_KEY]],
                ['meta_value' => $value]
            );

            PostMeta::reguard();
        }

        return $response;
    }
}
