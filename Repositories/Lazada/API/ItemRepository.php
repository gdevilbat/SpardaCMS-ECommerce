<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\Lazada\API;

use Gdevilbat\SpardaCMS\Modules\Ecommerce\Contracts\MarketPlaceItemInterface;
use Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\Lazada\Foundation\AbstractRepository;

use Spatie\ArrayToXml\ArrayToXml;

use Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\Product;
use Log;

/**
 * Class EloquentCoreRepository
 *
 * @package Gdevilbat\SpardaCMS\Modules\Core\Repositories\Eloquent
 */
class ItemRepository extends AbstractRepository implements MarketPlaceItemInterface
{
	public function getItemsList(array $request): Object
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

        return $data;
    }

    public function getItemDetail(array $request): Object
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

        return $item;
    }

    public function getVariations(array $request): Object
    {
        $this->validateRequest($request, [
          'item_id' => 'required',
        ]);

        $path = '/api/v1/item/tier_var/get';
        $parameter = $this->getPrimaryParameter($request['shop_id']);
        $parameter['item_id'] = $request['item_id'];
        $base_string = $this->getBaseString($path, $parameter);
        $sign = $this->getSignature($base_string);

        $res = $this->makeRequest($path, $parameter, $sign);

        $body = $res->getBody();

        if(empty($body))
            return response()->json(['message' => 'Check Connection'], 500);

        $data = json_decode($body);

        return $data;
    }

    public function addItem(array $request): Object
    {
        $this->validateRequest($request, [
            'Product' => 'required|array',
            'Product.PrimaryCategory' => 'required',
            'Product.Attributes' => 'required|array',
            'Product.Skus' => 'required|array',
            'Product.Skus.Sku' => 'required|array',
            'access_token' => 'required'
        ]);

        $path = '/product/create';
        $parameter = $this->getPrimaryParameter();
        $parameter['access_token'] =  $request['access_token'];

        $parameter['payload'] = ArrayToXml::convert(['Product' => $request['Product']], 'Request');

        $base_string = $this->getBaseString($path, $parameter);
        $sign = $this->getSignature($base_string);

        $res = $this->makeRequest(config('cms-ecommerce.LAZADA_API_URL'), $path, $parameter, $sign);

        $body = $res->getBody();

        if(empty($body))
            return response()->json(['message' => 'Check Connection'], 500);

        $data = json_decode($body);

        return $data;
    }

    public function itemUpdate(array $request): Object
    {
        $this->validateRequest($request, [
          'item_id' => 'required',
          'name' => 'required',
          'description' => 'required',
        ]);

        /*========================================
        =            Update Item Info            =
        ========================================*/
        
            $path = '/api/v1/item/update';
            $parameter = $this->getPrimaryParameter($request['shop_id']);
            $parameter['item_id'] = (int) $request['item_id'];
            $parameter['name'] = $request['name'];
            $parameter['description'] = $request['description'];

            $base_string = $this->getBaseString($path, $parameter);
            $sign = $this->getSignature($base_string);

            $res = $this->makeRequest($path, $parameter, $sign);

            $body = $res->getBody();

            if(empty($body))
                return response()->json(['message' => 'Check Connection'], 500);

            $data = json_decode($body);

        /*=====  End of Update Item Info  ======*/

        return $data;
    }

    public function itemUpdatePrice(array $request): Object
    {
        $this->validateRequest($request, [
          'item_id' => 'required',
          'price' => 'required',
        ]);

        $path = '/api/v1/items/update_price';
        $parameter = $this->getPrimaryParameter($request['shop_id']);
        $parameter['item_id'] = (int) $request['item_id'];
        $parameter['price'] = (int ) $request['price'];

        $base_string = $this->getBaseString($path, $parameter);
        $sign = $this->getSignature($base_string);

        $res = $this->makeRequest($path, $parameter, $sign);

        $body = $res->getBody();

        if(empty($body))
            return response()->json(['message' => 'Check Connection'], 500);

        $data = json_decode($body);

        return $data;
    }

    public function itemUpdateStock(array $request): Object
    {
        $this->validateRequest($request, [
          'item_id' => 'required',
          'stock' => 'required',
        ]);

        $path = '/api/v1/items/update_stock';
        $parameter = $this->getPrimaryParameter($request['shop_id']);
        $parameter['item_id'] = (int) $request['item_id'];
        $parameter['stock'] = (int) $request['stock'];

        $base_string = $this->getBaseString($path, $parameter);
        $sign = $this->getSignature($base_string);

        $res = $this->makeRequest($path, $parameter, $sign);

        $body = $res->getBody();

        if(empty($body))
            return response()->json(['message' => 'Check Connection'], 500);

        $data = json_decode($body);

        return $data;
    }

    public function itemInitTierVariations(array $request): Object
    {
        $this->validateRequest($request, [
          'item_id' => 'required',
          'tier_variation' => 'required|array',
          'tier_variation.*.name' => 'required',
          'tier_variation.*.options' => 'required|array',
          'tier_variation.*.options.*' => 'required',
          'variation' => 'required|array',
          'variation.*.tier_index' => 'required|array',
          'variation.*.stock' => 'required',
          'variation.*.price' => 'required'
        ]);

        $path = '/api/v1/item/tier_var/init';
        $parameter = $this->getPrimaryParameter($request['shop_id']);
        $parameter['item_id'] = $request['item_id'];
        $parameter['tier_variation'] = $request['tier_variation'];
        $parameter['variation'] = $request['variation'];
        $base_string = $this->getBaseString($path, $parameter);
        $sign = $this->getSignature($base_string);

        $res = $this->makeRequest($path, $parameter, $sign);

        $body = $res->getBody();

        if(empty($body))
            return response()->json(['message' => 'Check Connection'], 500);

        $data = json_decode($body);

        return $data;
    }

    public function itemAddTierVariations(array $request): Object
    {
        $this->validateRequest($request, [
          'item_id' => 'required',
          'variation' => 'required|array',
          'variation.*.tier_index' => 'required|array',
          'variation.*.stock' => 'required',
          'variation.*.price' => 'required'
        ]);

        $path = '/api/v1/item/tier_var/add';
        $parameter = $this->getPrimaryParameter($request['shop_id']);
        $parameter['item_id'] = $request['item_id'];
        $parameter['variation'] = $request['variation'];
        $base_string = $this->getBaseString($path, $parameter);
        $sign = $this->getSignature($base_string);

        $res = $this->makeRequest($path, $parameter, $sign);

        $body = $res->getBody();

        if(empty($body))
            return response()->json(['message' => 'Check Connection'], 500);

        $data = json_decode($body);

        return $data;
    }

    public function itemUpdateTierVariationList(array $request): Object
    {
        $this->validateRequest($request, [
          'item_id' => 'required',
          'tier_variation' => 'required|array',
          'tier_variation.*.name' => 'required',
          'tier_variation.*.options' => 'required|array',
          'tier_variation.*.options.*' => 'required',
        ]);

        $path = '/api/v1/item/tier_var/update_list';
        $parameter = $this->getPrimaryParameter($request['shop_id']);
        $parameter['item_id'] = $request['item_id'];
        $parameter['tier_variation'] = $request['tier_variation'];
        $base_string = $this->getBaseString($path, $parameter);
        $sign = $this->getSignature($base_string);

        $res = $this->makeRequest($path, $parameter, $sign);

        $body = $res->getBody();

        if(empty($body))
            return response()->json(['message' => 'Check Connection'], 500);

        $data = json_decode($body);

        return $data;
    }

    public function itemUpdateTierVariationIndex(array $request): Object
    {
        $this->validateRequest($request, [
          'item_id' => 'required',
          'variation' => 'required|array',
          'variation.*.tier_index' => 'required|array',
          'variation.*.variation_id' => 'required',
        ]);

        $path = '/api/v1/item/tier_var/update';
        $parameter = $this->getPrimaryParameter($request['shop_id']);
        $parameter['item_id'] = $request['item_id'];
        $parameter['variation'] = $request['variation'];
        $base_string = $this->getBaseString($path, $parameter);
        $sign = $this->getSignature($base_string);

        $res = $this->makeRequest($path, $parameter, $sign);

        $body = $res->getBody();

        if(empty($body))
            return response()->json(['message' => 'Check Connection'], 500);

        $data = json_decode($body);

        return $data;
    }

    public function itemUpdateVariationPriceBatch(array $request): Object
    {
        $this->validateRequest($request, [
          'variations' => 'required|array',
          'variations.*.price' => 'required',
          'variations.*.variation_id' => 'required',
          'variations.*.item_id' => 'required'
        ]);

        $path = '/api/v1/items/update/vars_price';
        $parameter = $this->getPrimaryParameter($request['shop_id']);
        $parameter['variations'] = $request['variations'];
        $base_string = $this->getBaseString($path, $parameter);
        $sign = $this->getSignature($base_string);

        $res = $this->makeRequest($path, $parameter, $sign);

        $body = $res->getBody();

        if(empty($body))
            return response()->json(['message' => 'Check Connection'], 500);

        $data = json_decode($body);

        return $data;
    }

    public function itemUpdateVariationStockBatch(array $request): Object
    {
        $this->validateRequest($request, [
          'variations' => 'required|array',
          'variations.*.stock' => 'required',
          'variations.*.variation_id' => 'required',
          'variations.*.item_id' => 'required'
        ]);

        $path = '/api/v1/items/update/vars_stock';
        $parameter = $this->getPrimaryParameter($request['shop_id']);
        $parameter['variations'] = $request['variations'];
        $base_string = $this->getBaseString($path, $parameter);
        $sign = $this->getSignature($base_string);

        $res = $this->makeRequest($path, $parameter, $sign);

        $body = $res->getBody();

        if(empty($body))
            return response()->json(['message' => 'Check Connection'], 500);

        $data = json_decode($body);

        return $data;
    }

    public function getBoostedItem(array $request): Object
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

        return $data;
    }

    public function setBoostedItem(array $request): Object
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

        return $data;
    }

    public function getCategories(array $request): Object
    {
        $this->validateRequest($request, [
        ]);

        $path = '/category/tree/get';
        $parameter = $this->getPrimaryParameter();

        $base_string = $this->getBaseString($path, $parameter);
        $sign = $this->getSignature($base_string);

        $res = $this->makeRequest(config('cms-ecommerce.LAZADA_API_URL') ,$path, $parameter, $sign, 'GET');

        $body = $res->getBody();

        if(empty($body))
            return response()->json(['message' => 'Check Connection'], 500);

        $data = json_decode($body);

        return $data;
    }

    public function getAttributes(array $request): Object
    {
        $this->validateRequest($request, [
        ]);

        $path = '/category/attributes/get';
        $parameter = $this->getPrimaryParameter();
        $parameter['primary_category_id'] = (integer) $request['category_id'];

        $base_string = $this->getBaseString($path, $parameter);
        $sign = $this->getSignature($base_string);

        $res = $this->makeRequest(config('cms-ecommerce.LAZADA_API_URL'), $path, $parameter, $sign, 'GET');

        $body = $res->getBody();

        if(empty($body))
            return response()->json(['message' => 'Check Connection'], 500);

        $data = json_decode($body);

        return $data;
    }
}
