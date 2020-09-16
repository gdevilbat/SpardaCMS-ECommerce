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

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function getItemDetail(Request $request)
    {
        $this->validate($request, [
          'shop_id' => 'required',
          'product_id' => 'required',
        ]);

        $path = '/api/v1/item/get';
        $parameter = $this->getPrimaryParameter($request->input('shop_id'));
        $parameter['item_id'] = (int) $request->input('product_id');

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

        try {
            $item = $data->item;
        } catch (\ErrorException $e) {
            log::info(json_encode($data));
            return response()->json(['message' => $data], 500);
        }

        $item->weight = 1000 * $item->weight;

        return response()->json($item);
    }

    public function itemUpdate(Request $request)
    {
        $this->validate($request, [
          'shop_id' => 'required',
          'product_id' => 'required',
          'post_id' => 'required'
        ]);

        $post = Product::with('productMeta')
                        ->findOrfail($request->post_id);

        $response = [];

        /*========================================
        =            Update Item Info            =
        ========================================*/
        
            $path = '/api/v1/item/update';
            $parameter = $this->getPrimaryParameter($request->input('shop_id'));
            $parameter['item_id'] = (int) $request->input('product_id');
            $parameter['name'] = $post->post_title;

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

            array_push($response, $data);

        
        /*=====  End of Update Item Info  ======*/

        /*========================================
        =            Update Item Info            =
        ========================================*/

            if($post->productMeta->product_sale > 0 && $post->productMeta->product_sale < $post->productMeta->product_price)
            {
                $price = $post->productMeta->product_sale;
            }
            else
            {
                $price = $post->productMeta->product_price;
            }
        
            $path = '/api/v1/items/update_price';
            $parameter = $this->getPrimaryParameter($request->input('shop_id'));
            $parameter['item_id'] = (int) $request->input('product_id');
            $parameter['price'] = (int )$price;

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

            array_push($response, $data);

        
        /*=====  End of Update Item Info  ======*/

        /*========================================
        =            Update Stock Info            =
        ========================================*/

            $availability = [Product::STAT_INSTOCK, Product::STAT_PREORDER];

            if (in_array( $post->productMeta->availability, $availability)) 
            {
                $stock = 7;
            }
            else
            {
                $stock = 0;
            }
        
            $path = '/api/v1/items/update_stock';
            $parameter = $this->getPrimaryParameter($request->input('shop_id'));
            $parameter['item_id'] = (int) $request->input('product_id');
            $parameter['stock'] = $stock;

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

            array_push($response, $data);

        
        /*=====  End of Update Item Info  ======*/

        return response()->json($response);
    }

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
}
