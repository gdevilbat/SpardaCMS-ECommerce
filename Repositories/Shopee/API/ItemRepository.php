<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\Shopee\API;

use Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\Shopee\Foundation\AbstractRepository;
use Illuminate\Http\Request;

use Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\Product;
use Log;

/**
 * Class EloquentCoreRepository
 *
 * @package Gdevilbat\SpardaCMS\Modules\Core\Repositories\Eloquent
 */
class ItemRepository extends AbstractRepository
{
	public function getItemsList(array $request)
    {
    	$this->validateRequest($request, [
	        'pagination_offset' => 'required',
	        'pagination_entries_per_page' => 'required',
        ]);

        $path = '/api/v1/items/get';
        $parameter = $this->getPrimaryParameter($request['shop_id']);
        $parameter['pagination_offset'] = (int) $request['pagination_offset'];
        $parameter['pagination_entries_per_page'] = (int) $request['pagination_entries_per_page'];

        $base_string = $this->getBaseString($path, $parameter);
        $sign = $this->getSignature($base_string);

        $res = $this->makeRequest($path, $parameter, $sign);

        $body = $res->getBody();

        if(empty($body))
            return response()->json(['message' => 'Check Connection'], 500);

        $data = json_decode($body);

        return response()->json($data);
    }

    public function getItemDetail(array $request)
    {
    	$this->validateRequest($request, [
	        'product_id' => 'required',
        ]);

        $path = '/api/v1/item/get';
        $parameter = $this->getPrimaryParameter($request['shop_id']);
        $parameter['item_id'] = (int) $request['product_id'];

        $base_string = $this->getBaseString($path, $parameter);
        $sign = $this->getSignature($base_string);

        $res = $this->makeRequest($path, $parameter, $sign);

        $body = $res->getBody();

        if(empty($body))
            return response()->json(['message' => 'Check Connection'], 500);

        $data = json_decode($body);

        try {
            $item = $data->item;
        } catch (\ErrorException $e) {
            log::info(json_encode($data));
            return response()->json(['message' => $data], 500);
        }

        $item->weight = 1000 * $item->weight;

        return response()->json($item);
    }

    public function addItem(array $request)
    {
        $this->validateRequest($request, [
            'name' => 'required',
            'category_id' => 'required',
            'description' => 'required',
            'price' => 'required',
            'stock' => 'required',
            'logistics' => 'required',
            'weight' => 'required',
            'images' => 'required',
            'attributes' => 'required',
            'condition' => 'required',
        ]);

        $path = '/api/v1/item/adds';
        $parameter = $this->getPrimaryParameter($request['shop_id']);
        $parameter['name'] = $request['name'];
        $parameter['category_id'] = $request['category_id'];
        $parameter['description'] = $request['description'];
        $parameter['price'] = $request['price'];
        $parameter['stock'] = $request['stock'];
        $parameter['logistics'] = $request['logistics'];
        $parameter['weight'] = $request['weight'];
        $parameter['images'] = $request['images'];
        $parameter['attributes'] = $request['attributes'];
        $parameter['condition'] = $request['condition'];

        if(array_key_exists('is_pre_order', $request))
        {
            $parameter['days_to_ship'] = $request['days_to_ship'];
            $parameter['is_pre_order'] = $request['is_pre_order'];
        }

        $base_string = $this->getBaseString($path, $parameter);
        $sign = $this->getSignature($base_string);

        $res = $this->makeRequest($path, $parameter, $sign);

        $body = $res->getBody();

        if(empty($body))
            return response()->json(['message' => 'Check Connection'], 500);

        $data = json_decode($body);

        return response()->json($data);
    }

    public function itemUpdate(array $request)
    {
        $this->validateRequest($request, [
          'product_id' => 'required',
          Product::FOREIGN_KEY => 'required'
        ]);

        $post = Product::with('productMeta')
                        ->findOrfail($request[Product::FOREIGN_KEY]);

        $response = [];

        /*========================================
        =            Update Item Info            =
        ========================================*/
        
            $path = '/api/v1/item/update';
            $parameter = $this->getPrimaryParameter($request['shop_id']);
            $parameter['item_id'] = (int) $request['product_id'];
            $parameter['name'] = $post->post_title;
            $parameter['description'] = html_entity_decode(strip_tags($post->post_content));

            $base_string = $this->getBaseString($path, $parameter);
            $sign = $this->getSignature($base_string);

            $res = $this->makeRequest($path, $parameter, $sign);

            $body = $res->getBody();

            if(empty($body))
                return response()->json(['message' => 'Check Connection'], 500);

            $data = json_decode($body);

            array_push($response, $data);

        
        /*=====  End of Update Item Info  ======*/

        /*========================================
        =            Update Item Info            =
        ========================================*/

            if($post->productMeta->product_sale > 0 && $post->productMeta->product_sale < $post->productMeta->product_price)
            {
                $price = $post->productMeta->product_sale;
            }
            else
            {
                $price = $post->productMeta->product_price;
            }
        
            $path = '/api/v1/items/update_price';
            $parameter = $this->getPrimaryParameter($request['shop_id']);
            $parameter['item_id'] = (int) $request['product_id'];
            $parameter['price'] = (int )$price;

            $base_string = $this->getBaseString($path, $parameter);
            $sign = $this->getSignature($base_string);

            $res = $this->makeRequest($path, $parameter, $sign);

            $body = $res->getBody();

            if(empty($body))
                return response()->json(['message' => 'Check Connection'], 500);

            $data = json_decode($body);

            array_push($response, $data);

        
        /*=====  End of Update Item Info  ======*/

        /*========================================
        =            Update Stock Info            =
        ========================================*/

            $availability = [Product::STAT_INSTOCK, Product::STAT_PREORDER];

            if (in_array( $post->productMeta->availability, $availability)) 
            {
                $stock = rand(5,9);
            }
            else
            {
                $stock = 0;
            }
        
            $path = '/api/v1/items/update_stock';
            $parameter = $this->getPrimaryParameter($request['shop_id']);
            $parameter['item_id'] = (int) $request['product_id'];
            $parameter['stock'] = $stock;

            $base_string = $this->getBaseString($path, $parameter);
            $sign = $this->getSignature($base_string);

            $res = $this->makeRequest($path, $parameter, $sign);

            $body = $res->getBody();

            if(empty($body))
                return response()->json(['message' => 'Check Connection'], 500);

            $data = json_decode($body);

            array_push($response, $data);

        
        /*=====  End of Update Item Info  ======*/

        return response()->json($response);
    }

    public function getBoostedItem(array $request)
    {
    	$this->validateRequest($request, [
        ]);

        $path = '/api/v1/items/get_boosted';
        $parameter = $this->getPrimaryParameter($request['shop_id']);

        $base_string = $this->getBaseString($path, $parameter);
        $sign = $this->getSignature($base_string);

        $res = $this->makeRequest($path, $parameter, $sign);

        $body = $res->getBody();

        if(empty($body))
            return response()->json(['message' => 'Check Connection'], 500);

        $data = json_decode($body);

        try {
            $items = $data->items;
        } catch (\ErrorException $e) {
            log::info(json_encode($data));
            return response()->json(['message' => $data], 500);
        }

        return response()->json($items);
    }

    public function setBoostedItem(array $request)
    {
    	$this->validateRequest($request, [
            'item_id' => 'required|array'
        ]);

        $path = '/api/v1/items/boost';
        $parameter = $this->getPrimaryParameter($request['shop_id']);
        $parameter['item_id'] = $request['item_id'];

        $base_string = $this->getBaseString($path, $parameter);
        $sign = $this->getSignature($base_string);

        $res = $this->makeRequest($path, $parameter, $sign);

        $body = $res->getBody();

        if(empty($body))
            return response()->json(['message' => 'Check Connection'], 500);

        $data = json_decode($body);

        return response()->json($data);
    }

    public function getCategories(array $request)
    {
        $this->validateRequest($request, [
            'language' => 'required|in:en,vi,th,zh-Hant,zh-Hans,ms-my,pt-br,id'
        ]);

        $path = '/api/v1/item/categories/get';
        $parameter = $this->getPrimaryParameter($request['shop_id']);
        $parameter['language'] = $request['language'];

        $base_string = $this->getBaseString($path, $parameter);
        $sign = $this->getSignature($base_string);

        $res = $this->makeRequest($path, $parameter, $sign);

        $body = $res->getBody();

        if(empty($body))
            return response()->json(['message' => 'Check Connection'], 500);

        $data = json_decode($body);

        return response()->json($data);
    }

    public function getAttributes(array $request)
    {
        $this->validateRequest($request, [
            'language' => 'required|in:en,vi,th,zh-Hant,zh-Hans,ms-my,pt-br,id',
            'category_id' => 'required'
        ]);

        $path = '/api/v1/item/attributes/get';
        $parameter = $this->getPrimaryParameter($request['shop_id']);
        $parameter['language'] = $request['language'];
        $parameter['category_id'] = (integer) $request['category_id'];

        $base_string = $this->getBaseString($path, $parameter);
        $sign = $this->getSignature($base_string);

        $res = $this->makeRequest($path, $parameter, $sign);

        $body = $res->getBody();

        if(empty($body))
            return response()->json(['message' => 'Check Connection'], 500);

        $data = json_decode($body);

        return response()->json($data);
    }
}
