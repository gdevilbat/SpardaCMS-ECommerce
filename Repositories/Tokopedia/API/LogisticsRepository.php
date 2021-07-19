<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\Tokopedia\API;

use Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\Tokopedia\Foundation\AbstractRepository;
use Illuminate\Http\Request;

use Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\Product;
use Gdevilbat\SpardaCMS\Modules\Ecommerce\Contracts\MarketPlaceLogisticsInterface;

use Log;

/**
 * Class EloquentCoreRepository
 *
 * @package Gdevilbat\SpardaCMS\Modules\Core\Repositories\Eloquent
 */
class LogisticsRepository extends AbstractRepository implements MarketPlaceLogisticsInterface
{
	public function getLogistics(Request $request): Object
    {
        $this->validateRequest($request, [
        ]);

        $path = '/api/v1/logistics/channel/get';
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
}
