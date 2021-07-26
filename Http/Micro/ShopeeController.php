<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Micro;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\Product;
use Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta;
use Gdevilbat\SpardaCMS\Modules\Post\Entities\PostMeta;
use Illuminate\Http\Exceptions\HttpResponseException;

use Log;

use Validator;

use MarketPlace;

class ShopeeController
{
    public function shopGetDetail(Request $request)
    {
        return response()->json(MarketPlace::driver('shopee')->shop->getShopDetail($request->input()));
    }

    public function itemGetList(Request $request)
    {
        return response()->json(MarketPlace::driver('shopee')->item->getItemsList($request->input()));
    }

    public function itemGetDetail(Request $request)
    {
        return response()->json(MarketPlace::driver('shopee')->item->getItemDetail($request->input()));
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

        $product_variant = $post->meta->getMetaData(ProductMeta::PRODUCT_VARIANT);

        if(empty($product_variant))
        {
            /*====================================
            =            Update Price            =
            ====================================*/

                /*if($post->productMeta->product_sale > 0 && $post->productMeta->product_sale < $post->productMeta->product_price)
                {
                    $price = $post->productMeta->product_sale;
                }
                else
                {
                }*/
                $price = $post->productMeta->product_price;

                $parameter = [];
            
                $parameter['shop_id'] = $request->input('shop_id');
                $parameter['item_id'] = (int) $request->input('product_id');
                $parameter['price'] = (int )$price;

                $data = MarketPlace::driver('shopee')->item->itemUpdatePrice($parameter);

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

                $data = MarketPlace::driver('shopee')->item->itemUpdateStock($parameter);

                array_push($response, $data);
            
            /*=====  End of Section comment block  ======*/
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

            array_push($response, $data);

            if(!property_exists($data, 'variation_id_list'))
            {
                $parameter = [];

                $parameter['shop_id'] = $request->input('shop_id');
                $parameter['item_id'] = (int) $request->input('product_id');
                $parameter['tier_variation'] = $tiers;

                $data = MarketPlace::driver('shopee')->item->itemUpdateTierVariationList($parameter);

                array_push($response, $data);

                $parameter = [];

                $parameter['shop_id'] = $request->input('shop_id');
                $parameter['item_id'] = (int) $request->input('product_id');

                $data = MarketPlace::driver('shopee')->item->getVariations($parameter);

                $response_variant = collect($data->variations);
                $sorted_variations = array_values($response_variant->sortBy('variation_id')->toArray());

                $update_variation = [];
                $add_variation = [];
                foreach ($variations as $key => $variation) 
                {
                    if(array_key_exists($key, $sorted_variations))
                    {
                        $update_variation[$key]['tier_index'] = $variations[$key]['tier_index']; 
                        $update_variation[$key]['variation_id'] = $sorted_variations[$key]->variation_id; 
                        $update_variation[$key]['item_id'] = (int) $request->input('product_id');
                        $update_variation[$key]['price'] = $variations[$key]['price']; 
                        $update_variation[$key]['stock'] = $variations[$key]['stock']; 
                    }
                    else
                    {
                        $add_variation[$key]['tier_index'] = $variations[$key]['tier_index']; 
                        $add_variation[$key]['price'] = $variations[$key]['price']; 
                        $add_variation[$key]['stock'] = $variations[$key]['stock']; 
                    }
                }

                if(!empty($update_variation))
                {
                    $parameter = [];

                    $parameter['shop_id'] = $request->input('shop_id');
                    $parameter['item_id'] = (int) $request->input('product_id');
                    $parameter['variation'] = $update_variation;

                    $data = MarketPlace::driver('shopee')->item->itemUpdateTierVariationIndex($parameter);

                    array_push($response, $data);

                    $parameter = [];

                    $parameter['shop_id'] = $request->input('shop_id');
                    $parameter['variations'] = $update_variation;

                    $data = MarketPlace::driver('shopee')->item->itemUpdateVariationPriceBatch($parameter);

                    array_push($response, $data);

                    $data = MarketPlace::driver('shopee')->item->itemUpdateVariationStockBatch($parameter);

                    array_push($response, $data);
                }

                if(!empty($add_variation))
                {
                    $add_variation = array_values($add_variation);
                    $parameter = [];

                    $parameter['shop_id'] = $request->input('shop_id');
                    $parameter['item_id'] = (int) $request->input('product_id');
                    $parameter['variation'] = $add_variation;

                    $data = MarketPlace::driver('shopee')->item->itemAddTierVariations($parameter);

                    array_push($response, $data);
                }

            }            

        }

        /*========================================
        =            Update Item Info            =
        ========================================*/

            $parameter = [];
        
            $parameter['shop_id'] = $request->input('shop_id');
            $parameter['item_id'] = (int) $request->input('product_id');
            $parameter['name'] = $post->post_title;
            $parameter['description'] = html_entity_decode(strip_tags($post->post_content));

            $data = MarketPlace::driver('shopee')->item->itemUpdate($parameter);

            $data_item = $data;

            array_push($response, $data_item);

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
        
        /*=====  End of Update Item Info  ======*/

        /*=============================================
        =            Update Item Promotion            =
        =============================================*/
        

            $discount_id = $data->item->discount_id;

            if($discount_id == 0)
            {
                $group = collect($data->item->variations)->groupBy('discount_id')->forget(0);

                if($group->count() > 0)
                {
                    $discount_id = $group->keys()->first();
                }
            }

            if($discount_id > 0)
            {
                $parameter = [];
            
                $parameter['shop_id'] = $request->input('shop_id');
                $parameter['items'][0]['item_id'] = (int) $request->input('product_id');
                $parameter['discount_id'] = $discount_id;

                $variant = $post->meta->getMetaData(ProductMeta::PRODUCT_VARIANT);

                $shopee = $post->meta->getMetaData(ProductMeta::SHOPEE_STORE);

                if(!empty($variant))
                {
                    $childrens = $variant->children;
                    $shopee_childrens = $shopee->children;

                    $i = 0;
                    foreach ($childrens as $key => $children) {
                        if((int) $children->sale > 0)
                        {
                            if(array_key_exists($key, $shopee_childrens))
                            {
                                $parameter['items'][0]['variations'][$i]['variation_id'] = $shopee_childrens[$key]->product_id;
                                $parameter['items'][0]['variations'][$i]['variation_promotion_price'] = $children->sale;
                                $i++;
                            }
                        }
                    }
                }


                if($post->productMeta->product_sale > 0)
                {
                    $parameter['items'][0]['item_promotion_price'] = $post->productMeta->product_sale;
                }

                $data = Marketplace::driver('shopee')->discount->updateDiscountItems($parameter);

                array_push($response, $data);
            }



        /*=====  End of Update Item Promotion  ======*/
        

        return response()->json($response);
    }

    public function itemGetBoosted(Request $request)
    {
        return response()->json(MarketPlace::driver('shopee')->item->getBoostedItem($request->input()));
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
        
            $images_data = MarketPlace::driver('shopee')->image->uploadImage($request->only('product_image', 'shop_id'));

            if(!property_exists($images_data, 'images')){
                throw new HttpResponseException(response()->json([
                    'errors'  => [
                        'msg' => [
                            $images_data->msg
                        ]
                    ]
                ], 422));
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

        $response = MarketPlace::driver('shopee')->item->addItem($data);
        $response = $response;

        if(!property_exists($response, 'item')){
            throw new HttpResponseException(response()->json([
                'errors'  => [
                    'msg' => [
                        $response->msg
                    ]
                ]
            ], 422));
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

    public function itemGetCategories(Request $request)
    {
        $request->merge([
            'language' => 'id'
        ]);

        $data = MarketPlace::driver('shopee')->item->getCategories($request->input());

        $content = $data;

        $tmp = collect($content->categories);

        $cat_data = $tmp->filter(function($value, $key){
            return $value->parent_id == 0;
        });

        $col_cat = collect(array_values($cat_data->toArray()))->sortBy('category_name');

        $self = $this;

        $categories = $col_cat->map(function($item, $key) use ($tmp, $self){
                            return $self->getCatChildren($item, $tmp);
                        });

        return array_values($categories->toArray());
    }

    public function itemGetAttributes(Request $request)
    {
        $request->merge([
            'language' => 'id'
        ]);

        $data = MarketPlace::driver('shopee')->item->getAttributes($request->input());

        $content = $data;

        $content = collect($content);

        return $content;
    }

    public function getLogistics(Request $request)
    {
        $request->merge([
            'language' => 'id'
        ]);

        $data = MarketPlace::driver('shopee')->logistics->getLogistics($request->input());

        $content = $data;

        $tmp = collect($content->logistics);

        $cat_data = $tmp->filter(function($value, $key){
            return $value->mask_channel_id == 0;
        });

        $col_log = collect(array_values($cat_data->toArray()));

        $self = $this;

        $logistics = $col_log->map(function($item, $key) use ($tmp, $self){
            return $self->getAttrChildren($item, $tmp);
        });

        return $logistics;
    }

    private function getCatChildren($item, $shop_cat)
    {
        $tmp = $shop_cat->filter(function($child, $key) use ($item){
            return $child->parent_id == $item->category_id; 
        });

        if($tmp->count() > 0)
        {
            $col_cat = collect(array_values($tmp->toArray()));
            $self = $this;

            $item->children = array_values($col_cat->map(function($item, $key) use ($shop_cat, $self){
                                return $self->getCatChildren($item, $shop_cat);
                            })
                            ->sortBy('category_name')
                            ->toArray());

        }
        else
        {
            $item->children = [];
        }

        return $item;
    }

    private function getAttrChildren($item, $shop_attr)
    {
        $tmp = $shop_attr->filter(function($child, $key) use ($item){
            return $child->mask_channel_id == $item->logistic_id; 
        });

        if($tmp->count() > 0)
        {
            $col_cat = collect(array_values($tmp->toArray()));
            $self = $this;

            $item->children = array_values($col_cat->map(function($item, $key) use ($shop_attr, $self){
                return $self->getAttrChildren($item, $shop_attr);
            })->toArray());

        }
        else
        {
            $item->children = [];
        }

        return $item;
    }

    private function addVariation(array $data)
    {
        Validator::validate($data, [
            Product::FOREIGN_KEY => 'required',
        ]);

        $data;

        $response = MarketPlace::driver('shopee')->item->itemInitTierVariations($data);
        $response_variant = $response;

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
