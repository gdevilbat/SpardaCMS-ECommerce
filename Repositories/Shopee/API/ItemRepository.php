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
            'shop_id' => 'required',
	        'pagination_offset' => 'required',
	        'pagination_entries_per_page' => 'required',
        ]);

        $path = '/api/v1/items/get';
        $parameter = $this->getPrimaryParameter($request['shop_id']);
        $parameter['pagination_offset'] = (int) $request['pagination_offset'];
        $parameter['pagination_entries_per_page'] = (int) $request['pagination_entries_per_page'];

        $base_string = SELF::URL.$path.'|'.json_encode($parameter);
        $sign = $this->getSignature($base_string);

        $client = new \GuzzleHttp\Client();
        $res = $client->request('POST', SELF::URL.$path, [
            'json' => $parameter,
            'headers' => [
                'Authorization' => $sign,
            ]
        ]);

        $body = $res->getBody();

        if(empty($body))
            return response()->json(['message' => 'Check Connection'], 500);

        $data = json_decode($body);

        return response()->json($data);
    }

    public function getItemDetail(array $request)
    {
    	$this->validateRequest($request, [
            'shop_id' => 'required',
	        'product_id' => 'required',
        ]);

        $path = '/api/v1/item/get';
        $parameter = $this->getPrimaryParameter($request['shop_id']);
        $parameter['item_id'] = (int) $request['product_id'];

        $base_string = SELF::URL.$path.'|'.json_encode($parameter);
        $sign = $this->getSignature($base_string);

        $client = new \GuzzleHttp\Client();
        $res = $client->request('POST', SELF::URL.$path, [
            'json' => $parameter,
            'headers' => [
                'Authorization' => $sign,
            ]
        ]);

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

    public function itemUpdate(array $request)
    {
        $this->validateRequest($request, [
          'shop_id' => 'required',
          'product_id' => 'required',
          'post_id' => 'required'
        ]);

        $post = Product::with('productMeta')
                        ->findOrfail($request['post_id']);

        $response = [];

        /*========================================
        =            Update Item Info            =
        ========================================*/
        
            $path = '/api/v1/item/update';
            $parameter = $this->getPrimaryParameter($request['shop_id']);
            $parameter['item_id'] = (int) $request['product_id'];
            $parameter['name'] = $post->post_title;

            $base_string = SELF::URL.$path.'|'.json_encode($parameter);
            $sign = $this->getSignature($base_string);

            $client = new \GuzzleHttp\Client();
            $res = $client->request('POST', SELF::URL.$path, [
                'json' => $parameter,
                'headers' => [
                    'Authorization' => $sign,
                ]
            ]);

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

            $base_string = SELF::URL.$path.'|'.json_encode($parameter);
            $sign = $this->getSignature($base_string);

            $client = new \GuzzleHttp\Client();
            $res = $client->request('POST', SELF::URL.$path, [
                'json' => $parameter,
                'headers' => [
                    'Authorization' => $sign,
                ]
            ]);

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

            $base_string = SELF::URL.$path.'|'.json_encode($parameter);
            $sign = $this->getSignature($base_string);

            $client = new \GuzzleHttp\Client();
            $res = $client->request('POST', SELF::URL.$path, [
                'json' => $parameter,
                'headers' => [
                    'Authorization' => $sign,
                ]
            ]);

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
            'shop_id' => 'required',
        ]);

        $path = '/api/v1/items/get_boosted';
        $parameter = $this->getPrimaryParameter($request['shop_id']);

        $base_string = SELF::URL.$path.'|'.json_encode($parameter);
        $sign = $this->getSignature($base_string);

        $client = new \GuzzleHttp\Client();
        $res = $client->request('POST', SELF::URL.$path, [
            'json' => $parameter,
            'headers' => [
                'Authorization' => $sign,
            ]
        ]);

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
            'shop_id' => 'required',
            'item_id' => 'required|array'
        ]);

        $path = '/api/v1/items/boost';
        $parameter = $this->getPrimaryParameter($request['shop_id']);
        $parameter['item_id'] = $request['item_id'];

        $base_string = SELF::URL.$path.'|'.json_encode($parameter);
        $sign = $this->getSignature($base_string);

        $client = new \GuzzleHttp\Client();
        $res = $client->request('POST', SELF::URL.$path, [
            'json' => $parameter,
            'headers' => [
                'Authorization' => $sign,
            ]
        ]);

        $body = $res->getBody();

        if(empty($body))
            return response()->json(['message' => 'Check Connection'], 500);

        $data = json_decode($body);

        return response()->json($data);
    }
}
