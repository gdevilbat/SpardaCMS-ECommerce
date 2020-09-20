<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\Shopee\Foundation;

use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;

use Validator;

/**
 * Class EloquentCoreRepository
 *
 * @package Gdevilbat\SpardaCMS\Modules\Core\Repositories\Eloquent
 */
abstract class AbstractRepository
{
    CONST URL = 'https://partner.shopeemobile.com';

	protected function getAccessToken(Request $request = null)
    {
        $time = \Carbon\Carbon::now()->timestamp;
        $path = '/api/v2/auth/token/get';
        $base_string = config('cms-ecommerce.SHOPEE_PARTNER_ID').$path.$time.$request->shop_id;
        $sign = $this->getSignature($base_string);
        $url = url(SELF::URL.$path.'?'.http_build_query(['partner_id' => config('cms-ecommerce.SHOPEE_PARTNER_ID'), 'shop_id' => $request->shop_id, 'timestamp' => $time, 'sign' => $sign,]));

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

    protected final function getSignature($base_string)
    {
      return hash_hmac('SHA256', $base_string, config('cms-ecommerce.SHOPEE_PARTNER_SECRET'));
    }

    protected final function validateRequest(Request $request, $parameter)
    {
        $validator = Validator::make($request->all(), $parameter);

        if ($validator->fails()) {
            throw new HttpResponseException(response()->json([
                'errors'  => $validator->errors(),
                'message' => 'The given data was invalid.',
            ], 422));
        }
    }
}
