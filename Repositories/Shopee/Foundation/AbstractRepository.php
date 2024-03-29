<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\Shopee\Foundation;

use Illuminate\Http\Exceptions\HttpResponseException;

use Validator;

/**
 * Class EloquentCoreRepository
 *
 * @package Gdevilbat\SpardaCMS\Modules\Core\Repositories\Eloquent
 */
abstract class AbstractRepository
{
	final public function getAccessToken($shop_id)
    {
        $time = \Carbon\Carbon::now()->timestamp;
        $path = '/api/v2/auth/token/get';
        $base_string = config('cms-ecommerce.SHOPEE_PARTNER_ID').$path.$time.$request->shop_id;
        $sign = $this->getSignature($base_string);
        $url = url(config('cms-ecommerce.SHOPEE_API_URL').$path.'?'.http_build_query(['partner_id' => config('cms-ecommerce.SHOPEE_PARTNER_ID'), 'shop_id' => $request->shop_id, 'timestamp' => $time, 'sign' => $sign,]));

        $client = new \GuzzleHttp\Client();
        $res = $client->request('POST', $url, [
            'json' => [
                        'code' => $request->code,
                        'shop_id' => (int) $request->shop_id,
                        'partner_id' => config('cms-ecommerce.SHOPEE_PARTNER_ID')
            ]
        ]);

        $body = $res->getBody();

        return json_decode($body)->access_token;
    }

    protected final function getPrimaryParameter($shop_id)
    {
      $time = \Carbon\Carbon::now()->timestamp;

      return [
        'partner_id' => (int) config('cms-ecommerce.SHOPEE_PARTNER_ID'),
        'timestamp' => $time,
        'shopid' => (int) $shop_id
      ];
    }

    public final function getSignature($base_string)
    {
      return hash_hmac('SHA256', $base_string, config('cms-ecommerce.SHOPEE_PARTNER_SECRET'));
    }

    public final function getBaseString($path, array $parameter)
    {
        return config('cms-ecommerce.SHOPEE_API_URL').$path.'|'.json_encode($parameter);
    }

    public final function makeRequest(string $url, string $path, array $parameter, string $sign, string $method = 'POST')
    {
        $client = new \GuzzleHttp\Client();
        return $res = $client->request($method, config('cms-ecommerce.SHOPEE_API_URL').$path, [
                    'json' => $parameter,
                    'headers' => [
                        'Authorization' => $sign,
                    ]
                ]);
    }

    public final function validateRequest(array $request, array $parameter)
    {
        $mandatory = [
            'shop_id' => 'required'
        ];

        $data = array_merge($mandatory, $parameter);

        $validator = Validator::make($request, $data);

        $validator->validate();
    }
}
