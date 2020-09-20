<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Micro;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\Product;

use Log;

class ShopeeController
{
    protected $shopeeRepository;

    public function __construct()
    {
        $this->shopeeRepository = new \Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\Shopee\ShopeeRepository;
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function itemGetDetail(Request $request)
    {
        return $this->shopeeRepository->item->getItemDetail($request);
    }

    public function itemUpdate(Request $request)
    {
        return $this->shopeeRepository->item->itemUpdate($request);
    }

    public function getBoostedItem(Request $request)
    {
        $this->validate($request, [
          'shop_id' => 'required',
          'product_id' => 'required',
          'post_id' => 'required'
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

        return response()->json($data);
    }
}
