<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\Shopee\API;

use Gdevilbat\SpardaCMS\Modules\Ecommerce\Contracts\MarketPlaceItemInterface;
use Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\Shopee\Foundation\AbstractRepository;

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

        $res = $this->makeRequest(config('cms-ecommerce.SHOPEE_API_URL'), $path, $parameter, $sign);

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

        $res = $this->makeRequest(config('cms-ecommerce.SHOPEE_API_URL'), $path, $parameter, $sign);

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

        $res = $this->makeRequest(config('cms-ecommerce.SHOPEE_API_URL'), $path, $parameter, $sign);

        $body = $res->getBody();

        if(empty($body))
            return response()->json(['message' => 'Check Connection'], 500);

        $data = json_decode($body);

        return $data;
    }

    public function addItem(array $request): Object
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
            'days_to_ship' => 'required_if:is_pre_order,true'
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
            $parameter['is_pre_order'] = $request['is_pre_order'];
        }

        if(array_key_exists('days_to_ship', $request))
        {
            $parameter['days_to_ship'] = $request['days_to_ship'];
        }

        $base_string = $this->getBaseString($path, $parameter);
        $sign = $this->getSignature($base_string);

        $res = $this->makeRequest(config('cms-ecommerce.SHOPEE_API_URL'), $path, $parameter, $sign);

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
          'days_to_ship' => 'required_if:is_pre_order,true'
        ]);

        /*========================================
        =            Update Item Info            =
        ========================================*/
        
            $path = '/api/v1/item/update';
            $parameter = $this->getPrimaryParameter($request['shop_id']);
            $parameter['item_id'] = (int) $request['item_id'];
            $parameter['name'] = $request['name'];
            $parameter['description'] = $request['description'];

            if(array_key_exists('is_pre_order', $request))
            {
                $parameter['is_pre_order'] = $request['is_pre_order'];
            }

            if(array_key_exists('days_to_ship', $request))
            {
                $parameter['days_to_ship'] = $request['days_to_ship'];
            }

            $base_string = $this->getBaseString($path, $parameter);
            $sign = $this->getSignature($base_string);

            $res = $this->makeRequest(config('cms-ecommerce.SHOPEE_API_URL'), $path, $parameter, $sign);

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

        $res = $this->makeRequest(config('cms-ecommerce.SHOPEE_API_URL'), $path, $parameter, $sign);

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

        $res = $this->makeRequest(config('cms-ecommerce.SHOPEE_API_URL'), $path, $parameter, $sign);

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

        $res = $this->makeRequest(config('cms-ecommerce.SHOPEE_API_URL'), $path, $parameter, $sign);

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

        $res = $this->makeRequest(config('cms-ecommerce.SHOPEE_API_URL'), $path, $parameter, $sign);

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

        $res = $this->makeRequest(config('cms-ecommerce.SHOPEE_API_URL'), $path, $parameter, $sign);

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

        $res = $this->makeRequest(config('cms-ecommerce.SHOPEE_API_URL'), $path, $parameter, $sign);

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

        $res = $this->makeRequest(config('cms-ecommerce.SHOPEE_API_URL'), $path, $parameter, $sign);

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

        $res = $this->makeRequest(config('cms-ecommerce.SHOPEE_API_URL'), $path, $parameter, $sign);

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

        $res = $this->makeRequest(config('cms-ecommerce.SHOPEE_API_URL'), $path, $parameter, $sign);

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

        $res = $this->makeRequest(config('cms-ecommerce.SHOPEE_API_URL'), $path, $parameter, $sign);

        $body = $res->getBody();

        if(empty($body))
            return response()->json(['message' => 'Check Connection'], 500);

        $data = json_decode($body);

        return $data;
    }

    public function getCategories(array $request): Object
    {
        $this->validateRequest($request, [
            'language' => 'required|in:en,vi,th,zh-Hant,zh-Hans,ms-my,pt-br,id'
        ]);

        $path = '/api/v1/item/categories/get';
        $parameter = $this->getPrimaryParameter($request['shop_id']);
        $parameter['language'] = $request['language'];

        $base_string = $this->getBaseString($path, $parameter);
        $sign = $this->getSignature($base_string);

        $res = $this->makeRequest(config('cms-ecommerce.SHOPEE_API_URL'), $path, $parameter, $sign);

        $body = $res->getBody();

        if(empty($body))
            return response()->json(['message' => 'Check Connection'], 500);

        $data = json_decode($body);

        return $data;
    }

    public function getAttributes(array $request): Object
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

        $res = $this->makeRequest(config('cms-ecommerce.SHOPEE_API_URL'), $path, $parameter, $sign);

        $body = $res->getBody();

        if(empty($body))
            return response()->json(['message' => 'Check Connection'], 500);

        $data = json_decode($body);

        return $data;
    }
}
