<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\Shopee\API;

use Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\Shopee\Foundation\AbstractRepository;
use Illuminate\Http\Request;

use Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\Product;
use Log;

use Gdevilbat\SpardaCMS\Modules\Ecommerce\Contracts\MarketPlaceShopInterface;

/**
 * Class EloquentCoreRepository
 *
 * @package Gdevilbat\SpardaCMS\Modules\Core\Repositories\Eloquent
 */
class ShopRepository extends AbstractRepository implements MarketPlaceShopInterface
{
	/**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function getShopDetail(Request $request): Object
    {
    	$this->validateRequest($request, [
        ]);

        $path = '/api/v1/shop/get';
        $parameter = $this->getPrimaryParameter($request['shop_id']);

        $base_string = $this->getBaseString($path, $parameter);
        $sign = $this->getSignature($base_string);

        $res = $this->makeRequest($path, $parameter, $sign);

        $body = $res->getBody();

        if(empty($body))
            return response()->json(['message' => 'Check Connection'], 500);

        $data = json_decode($body);

        return $data;
    }

    public function getAuthUrl($callback)
    {
        $time = \Carbon\Carbon::now()->timestamp;
        $path = '/api/v2/shop/auth_partner';
        $base_string =  config('cms-ecommerce.SHOPEE_PARTNER_ID').$path.$time;
        $sign = $this->getSignature($base_string);
        $url = url(config('cms-ecommerce.SHOPEE_API_URL').$path.'?'.http_build_query(['partner_id' => config('cms-ecommerce.SHOPEE_PARTNER_ID'), 'redirect' => $callback, 'timestamp' => $time, 'sign' => $sign]));
        return redirect($url);
    }
}
