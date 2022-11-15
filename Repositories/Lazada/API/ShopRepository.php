<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\Lazada\API;

use Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\Lazada\Foundation\AbstractRepository;

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
    public function getShopDetail(array $request): Object
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
        $path = '/oauth/authorize';
        $url = url(config('cms-ecommerce.LAZADA_AUTH_URL').$path.'?'.http_build_query(['client_id' => config('cms-ecommerce.LAZADA_PARTNER_ID'), 'redirect_url' => $callback, 'response_type' => 'code', 'force_auth' => 'true']));
        return redirect($url);
    }
}
