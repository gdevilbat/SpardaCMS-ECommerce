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
class ShopRepository extends AbstractRepository
{
	/**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function getShopDetail(array $request)
    {
    	$this->validateRequest($request, [
            'shop_id' => 'required',
        ]);

        $path = '/api/v1/shop/get';
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

        return response()->json($data);
    }
}
