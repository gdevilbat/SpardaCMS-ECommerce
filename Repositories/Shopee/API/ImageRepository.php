<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\Shopee\API;

use Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\Shopee\Foundation\AbstractRepository;

use Gdevilbat\SpardaCMS\Modules\Ecommerce\Contracts\MarketPlaceImageInterface;

use Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\Product;
use Log;

/**
 * Class EloquentCoreRepository
 *
 * @package Gdevilbat\SpardaCMS\Modules\Core\Repositories\Eloquent
 */
class ImageRepository extends AbstractRepository implements MarketPlaceImageInterface
{
	public function uploadImage(array $request): Object
    {
    	$this->validateRequest($request, [
	        'product_image' => 'required|array',
            'product_image.*' => 'required|url',
        ]);

        $path = '/api/v1/image/upload';
        $parameter = $this->getPrimaryParameter($request['shop_id']);
        $parameter['images'] =  $request['product_image'];

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
