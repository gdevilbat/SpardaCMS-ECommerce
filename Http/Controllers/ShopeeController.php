<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

use Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\Product;

use Log;

class ShopeeController extends Controller
{
    CONST URL = 'https://partner.shopeemobile.com';

    public function authentication()
    {
        $time = \Carbon\Carbon::now()->timestamp;
        $path = '/api/v2/shop/auth_partner';
        $base_string =  config('cms-ecommerce.SHOPEE_PARTNER_ID').$path.$time;
        $sign = $this->getSignature($base_string);
        $url = url(SELF::URL.$path.'?'.http_build_query(['partner_id' => config('cms-ecommerce.SHOPEE_PARTNER_ID'), 'redirect' => url(action('\\'.Self::class.'@callback')), 'timestamp' => $time, 'sign' => $sign]));
        return redirect($url);
    }

    public function callback(Request $request)
    {
        return '<script>if (window.opener){window.close()}</script>';
    }

    protected final function getSignature($base_string)
    {
      return hash_hmac('SHA256', $base_string, config('cms-ecommerce.SHOPEE_PARTNER_SECRET'));
    }
}
