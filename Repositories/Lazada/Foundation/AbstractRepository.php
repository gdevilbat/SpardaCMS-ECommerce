<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\Lazada\Foundation;

use Illuminate\Http\Exceptions\HttpResponseException;

use Validator;

/**
 * Class EloquentCoreRepository
 *
 * @package Gdevilbat\SpardaCMS\Modules\Core\Repositories\Eloquent
 */
abstract class AbstractRepository
{
    CONST SIGN_METHOD = 'sha256';

	final public function getAccessToken($shop_id)
    {
        return request()->session()->get('lazada.'.$shop_id.'.access_token');
    }

    final public function requestToken($code)
    {
        $path = '/auth/token/create';
        $parameter = $this->getPrimaryParameter();
        $parameter['code'] = $code;

        $base_string = $this->getBaseString($path, $parameter);
        $sign = $this->getSignature($base_string);

        $res = $this->makeRequest(config('cms-ecommerce.LAZADA_AUTH_URL').'/rest', $path, $parameter, $sign);

        $body = json_decode($res->getBody());

        request()->session()->put('lazada.'.$body->country_user_info[0]->seller_id, ['access_token' => $body->access_token, 'refresh_token' => $body->refresh_token]);

        return $body;
    }

    final public function refreshToken($shop_id)
    {
        $path = '/auth/token/refresh';
        $parameter = $this->getPrimaryParameter();
        $parameter['refresh_token'] = request()->session()->get('lazada.'.$shop_id.'.refresh_token');

        $base_string = $this->getBaseString($path, $parameter);
        $sign = $this->getSignature($base_string);

        $res = $this->makeRequest(config('cms-ecommerce.LAZADA_AUTH_URL').'/rest', $path, $parameter, $sign);

        $body = json_decode($res->getBody());

        request()->session()->put('lazada.'.$body->country_user_info[0]->seller_id, ['access_token' => $body->access_token, 'refresh_token' => $body->refresh_token]);

        return $body;
    }

    protected final function getPrimaryParameter()
    {
      $time = \Carbon\Carbon::now()->timestamp;

      return [
        'app_key' => config('cms-ecommerce.LAZADA_PARTNER_ID'),
        'timestamp' => $this->msectime(),
        'sign_method' => SELF::SIGN_METHOD
      ];
    }

    public final function getSignature($base_string)
    {
      return strtoupper(hash_hmac(SELF::SIGN_METHOD, $base_string, config('cms-ecommerce.LAZADA_PARTNER_SECRET')));
    }

    public final function getBaseString($path, array $parameter)
    {
        ksort($parameter);

        $stringToBeSigned = '';
        $stringToBeSigned .= $path;
        foreach ($parameter as $k => $v)
        {
            $stringToBeSigned .= "$k$v";
        }
        unset($k, $v);

        return $stringToBeSigned;
    }

    public final function makeRequest(string $url, string $path, array $parameter, string $sign, string $method = 'POST')
    {
        $client = new \GuzzleHttp\Client();
        $parameter = array_merge($parameter, ['sign' => $sign]);

        $requestUrl = $url;
        $requestUrl .= $path;
        $requestUrl .= '?';

        foreach ($parameter as $sysParamKey => $sysParamValue)
        {
            $requestUrl .= "$sysParamKey=" . urlencode($sysParamValue) . "&";
        }

        $requestUrl = substr($requestUrl, 0, -1);

        return $res = $client->request($method, $requestUrl);
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

    protected final function msectime() {
       list($msec, $sec) = explode(' ', microtime());
       return $sec . '000';
    }
}
