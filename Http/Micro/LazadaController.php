<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Micro;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\Exceptions\HttpResponseException;

use Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\Product;
use Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta;
use Gdevilbat\SpardaCMS\Modules\Post\Entities\PostMeta;

use Log;

use Validator;

use MarketPlace;

class LazadaController
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

                if($post->productMeta->product_sale > 0 && $post->productMeta->product_sale < $post->productMeta->product_price)
                {
                    $price = $post->productMeta->product_sale;
                }
                else
                {
                    $price = $post->productMeta->product_price;
                }

                $req_prm = clone $request;

                $parameter = [];
            
                $parameter['shop_id'] = $request->input('shop_id');
                $parameter['item_id'] = (int) $request->input('product_id');
                $parameter['price'] = (int )$price;

                $req_prm->merge($parameter);

                $data = MarketPlace::driver('shopee')->item->itemUpdatePrice($req_prm);

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

                $req_prm = clone $request;
            
                $parameter = [];
            
                $parameter['shop_id'] = $request->input('shop_id');
                $parameter['item_id'] = (int) $request->input('product_id');
                $parameter['stock'] = (int ) $stock;

                $req_prm->merge($parameter);

                $data = MarketPlace::driver('shopee')->item->itemUpdateStock($req_prm);

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
                $req_prm = clone $request;

                $parameter = [];

                $parameter['shop_id'] = $request->input('shop_id');
                $parameter['item_id'] = (int) $request->input('product_id');
                $parameter['tier_variation'] = $tiers;

                $req_prm->merge($parameter);

                $data = MarketPlace::driver('shopee')->item->itemUpdateTierVariationList($req_prm);

                array_push($response, $data);

                $req_prm = clone $request;

                $parameter = [];

                $parameter['shop_id'] = $request->input('shop_id');
                $parameter['item_id'] = (int) $request->input('product_id');

                $req_prm->merge($parameter);

                $data = MarketPlace::driver('shopee')->item->getVariations($req_prm);


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
                    $req_prm = clone $request;

                    $parameter = [];

                    $parameter['shop_id'] = $request->input('shop_id');
                    $parameter['item_id'] = (int) $request->input('product_id');
                    $parameter['variation'] = $update_variation;

                    $req_prm->merge($parameter);

                    $data = MarketPlace::driver('shopee')->item->itemUpdateTierVariationIndex($req_prm);

                    array_push($response, $data);

                    $req_prm = clone $request;

                    $parameter = [];

                    $parameter['shop_id'] = $request->input('shop_id');
                    $parameter['variations'] = $update_variation;

                    $req_prm->merge($parameter);

                    $data = MarketPlace::driver('shopee')->item->itemUpdateVariationPriceBatch($req_prm);

                    array_push($response, $data);

                    $data = MarketPlace::driver('shopee')->item->itemUpdateVariationStockBatch($req_prm);

                    array_push($response, $data);
                }

                if(!empty($add_variation))
                {
                    $add_variation = array_values($add_variation);
                    $req_prm = clone $request;

                    $parameter = [];

                    $parameter['shop_id'] = $request->input('shop_id');
                    $parameter['item_id'] = (int) $request->input('product_id');
                    $parameter['variation'] = $add_variation;

                    $req_prm->merge($parameter);

                    $data = MarketPlace::driver('shopee')->item->itemAddTierVariations($req_prm);

                    array_push($response, $data);
                }

            }            

        }

        /*========================================
        =            Update Item Info            =
        ========================================*/

            $req_prm = clone $request;

            $parameter = [];
        
            $parameter['shop_id'] = $request->input('shop_id');
            $parameter['item_id'] = (int) $request->input('product_id');
            $parameter['name'] = $post->post_title;
            $parameter['description'] = html_entity_decode(strip_tags($post->post_content));

            $req_prm->merge($parameter);

            $data = MarketPlace::driver('shopee')->item->itemUpdate($req_prm);

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

        $data['shop_id'] = $request->shop_id;
        $data['Product']['PrimaryCategory'] = (integer) $request->input('category_id');

        /*=========================================
        =            Parsing Attribute            =
        =========================================*/
        
            $tmp = collect($request->input('product_attributes'));

            $filtetered_attributes =  $tmp->filter(function ($attribute, $key) {
                return !empty($attribute['value']);
            });

            $attributes = array_values($filtetered_attributes->toArray());

            foreach ($attributes as $key => $value) {
                if($value['attribute_type'] == 'normal')
                {
                    $data['Product']['Attributes'][$value['attribute_name']] = $value['value'];
                }

                if($value['attribute_type'] == 'sku')
                {
                    $data['Product']['Skus']['Sku'][$value['attribute_name']] = $value['value'];
                }
            }

        
        /*=====  End of Parsing Attribute  ======*/


        /*=====================================
        =            Parsing Image            =
        =====================================*/
        
            $images_data = MarketPlace::driver('lazada')->image->uploadImage($request->input());

            if(property_exists($images_data, 'errors')){
                throw new HttpResponseException(response()->json([
                    'errors'  => $images_data->errors
                ], 422));
            }

            foreach ($images_data->images as $image) {
                $data['Product']['Images']['Url'][] = $image->url;    
            }
        
        /*=====  End of Parsing Image  ======*/

        $request->merge($data);

        $response = MarketPlace::driver('lazada')->item->addItem($request->input());

        if(!property_exists($response, 'data')){
            throw new HttpResponseException(response()->json([
                'errors'  => [
                    'msg' => [
                        $response->detail
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

        $data = MarketPlace::driver('lazada')->item->getCategories($request->input());

        $content = $data;

        $categories = collect($content->data)->sortBy('name');

        return array_values($categories->toArray());
    }

    public function itemGetAttributes(Request $request)
    {
        $request->merge([
            'language' => 'id'
        ]);

        $data = MarketPlace::driver('lazada')->item->getAttributes($request->input());

        $content = $data;

        $content = collect($content->data);

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

        $request = resolve(Request::class);
        $request->merge($data);

        $response = MarketPlace::driver('shopee')->item->itemInitTierVariations($request->input());
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
