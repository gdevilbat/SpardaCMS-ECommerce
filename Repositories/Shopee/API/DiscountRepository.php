<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\Shopee\API;

use Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\Shopee\Foundation\AbstractRepository;
use Illuminate\Http\Exceptions\HttpResponseException;

use Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\Product;
use Gdevilbat\SpardaCMS\Modules\Ecommerce\Contracts\MarketPlaceDiscountInterface;

use Log;

/**
 * Class EloquentCoreRepository
 *
 * @package Gdevilbat\SpardaCMS\Modules\Core\Repositories\Eloquent
 */
class DiscountRepository extends AbstractRepository implements MarketPlaceDiscountInterface
{
	public function getDiscountsList(array $request): Object
    {
        $this->validateRequest($request, [
            'discount_status' => 'required|in:UPCOMING,ONGOING,EXPIRED,ALL',
            'pagination_offset' => 'required',
            'pagination_entries_per_page' => 'required'
        ]);

        $path = '/api/v1/discounts/get';
        $parameter = $this->getPrimaryParameter($request['shop_id']);
        $parameter['discount_status'] = $request['discount_status'];
        $parameter['pagination_offset'] = $request['pagination_offset'];
        $parameter['pagination_entries_per_page'] = $request['pagination_entries_per_page'];

        $base_string = $this->getBaseString($path, $parameter);
        $sign = $this->getSignature($base_string);

        $res = $this->makeRequest(config('cms-ecommerce.SHOPEE_API_URL'), $path, $parameter, $sign);

        $body = $res->getBody();

        if(empty($body))
            return response()->json(['message' => 'Check Connection'], 500);

        $data = json_decode($body);

        if(!property_exists($data, 'discount')){
            throw new HttpResponseException(response()->json([
                'errors'  => [
                    'msg' => [
                        $data->msg
                    ]
                ]
            ], 422));
        }

        return $data;
    }

    public function getDiscountDetail(array $request): Object
    {
        $this->validateRequest($request, [
            'discount_id' => 'required',
            'pagination_offset' => 'required',
            'pagination_entries_per_page' => 'required'
        ]);

        $path = '/api/v1/discount/detail';
        $parameter = $this->getPrimaryParameter($request['shop_id']);
        $parameter['discount_id'] = $request['discount_id'];
        $parameter['pagination_offset'] = $request['pagination_offset'];
        $parameter['pagination_entries_per_page'] = $request['pagination_entries_per_page'];

        $base_string = $this->getBaseString($path, $parameter);
        $sign = $this->getSignature($base_string);

        $res = $this->makeRequest(config('cms-ecommerce.SHOPEE_API_URL'), $path, $parameter, $sign);

        $body = $res->getBody();

        if(empty($body))
            return response()->json(['message' => 'Check Connection'], 500);

        $data = json_decode($body);

        if(!property_exists($data, 'items')){
            throw new HttpResponseException(response()->json([
                'errors'  => [
                    'msg' => [
                        $data->msg
                    ]
                ]
            ], 422));
        }

        return $data;
    }

    public function addDiscountItems(array $request): Object
    {
        $this->validateRequest($request, [
            'discount_id' => 'required',
            'items' => 'required|array',
            'items.*.item_id' => 'required',
            'items.*.item_promotion_price' => 'numeric',
            'items.*.purchase_limit' => 'required|numeric',
            'items.*.variations' => 'array',
            'items.*.variations.*.variation_id' => 'required',
            'items.*.variations.*.variation_promotion_price' => 'required|numeric',
            'items.*.variations.*.variation_promotion_stock' => 'numeric',
        ]);

        $path = '/api/v1/discount/items/add';
        $parameter = $this->getPrimaryParameter($request['shop_id']);
        $parameter['discount_id'] = $request['discount_id'];
        $parameter['items'] = $request['items'];

        $base_string = $this->getBaseString($path, $parameter);
        $sign = $this->getSignature($base_string);

        $res = $this->makeRequest(config('cms-ecommerce.SHOPEE_API_URL'), $path, $parameter, $sign);

        $body = $res->getBody();

        if(empty($body))
            return response()->json(['message' => 'Check Connection'], 500);

        $data = json_decode($body);

        return $data;
    }

    public function updateDiscountItems(array $request): Object
    {
        $this->validateRequest($request, [
            'discount_id' => 'required',
            'items' => 'required|array',
            'items.*.item_id' => 'required',
            'items.*.item_promotion_price' => 'numeric',
            'items.*.purchase_limit' => 'numeric',
            'items.*.variations' => 'array',
            'items.*.variations.*.variation_id' => 'required',
            'items.*.variations.*.variation_promotion_price' => 'required|numeric',
        ]);

        $path = '/api/v1/discount/items/update';
        $parameter = $this->getPrimaryParameter($request['shop_id']);
        $parameter['discount_id'] = $request['discount_id'];
        $parameter['items'] = $request['items'];

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
