<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\Lazada\API;

use Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\Lazada\Foundation\AbstractRepository;
use Illuminate\Http\Exceptions\HttpResponseException;

use Gdevilbat\SpardaCMS\Modules\Ecommerce\Contracts\MarketPlaceImageInterface;
use Spatie\ArrayToXml\ArrayToXml;

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
            'access_token' => 'required'
        ]);

        $path = '/images/migrate';
        $parameter = $this->getPrimaryParameter();
        $parameter['access_token'] =  $request['access_token'];

        $images;

        foreach ($request['product_image'] as $key => $value) {
            $images['Images']['Url'][$key] = $value;
        }

        $parameter['payload'] = ArrayToXml::convert($images, 'Request');

        $base_string = $this->getBaseString($path, $parameter);
        $sign = $this->getSignature($base_string);

        $res = $this->makeRequest(config('cms-ecommerce.LAZADA_API_URL') , $path, $parameter, $sign);

        $body = $res->getBody();

        if(empty($body))
            return response()->json(['message' => 'Check Connection'], 500);

        $data = json_decode($body);

        if(!property_exists($data, 'batch_id')){
            throw new HttpResponseException(response()->json([
                'errors'  => [
                    'msg' => [
                        $images_data->msg
                    ]
                ]
            ], 422));
        }

        $path = '/image/response/get';
        $parameter = $this->getPrimaryParameter();
        $parameter['access_token'] =  $request['access_token'];
        $parameter['batch_id'] = $data->batch_id;

        $base_string = $this->getBaseString($path, $parameter);
        $sign = $this->getSignature($base_string);

        $res = $this->makeRequest(config('cms-ecommerce.LAZADA_API_URL') , $path, $parameter, $sign, 'GET');

        $body = $res->getBody();

        if(empty($body))
            return response()->json(['message' => 'Check Connection'], 500);

        $data = json_decode($body);

        if(!property_exists($data, 'data')){
            throw new HttpResponseException(response()->json([
                'errors'  => [
                    'msg' => [
                        $data->message
                    ]
                ]
            ], 422));
        }

        return $data->data;
    }
}
