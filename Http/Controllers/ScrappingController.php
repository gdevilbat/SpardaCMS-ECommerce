<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

use MarketPlace;

class ScrappingController extends Controller
{
    public function scrappingTokopediaProductDetail(Request $request)
    {
        $request->merge([
            'shop_id' => $request->merchant
        ]);

        return response()->json(MarketPlace::driver('tokopedia')->item->getItemDetail($request));
    }

    public function scrappingTokopediaProductVariant(Request $request)
    {
        return response()->json(MarketPlace::driver('tokopedia')->item->getVariations($request));
    }

    public function scrappingShopee(Request $request)
    {
       $this->validate($request, [
          'shopid' => 'required',
          'itemid' => 'required',
        ]);

      /* $request->merge([
          'shop_id' => $request->shopid,
          'product_id' => $request->itemid,
       ]);

        $data = resolve(\Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\Shopee\ShopeeRepository::class)->item->getItemDetail($request->except(['itemid', 'shopid']));

        $data = json_decode($data->getContent());

        $content['item'] = $data;

        $content = collect($content);

        return $content;*/


        $url = 'https://shopee.co.id/api/v2/item/get?'.http_build_query(['itemid' => $request->itemid, 'shopid' => $request->shopid]);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_ENCODING => "",
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
              'if-none-match-: '.getSettingConfig('scrapping', 'shopee_none_match'),
              'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36',
              'cookie: '.getSettingConfig('scrapping', 'shopee_session')
            ),
          ));

        $response = curl_exec($curl);

        curl_close($curl);

        if(empty($response))
            return response()->json(['message' => 'Check Connection'], 500);

        $response = json_decode($response);

        if(!empty($response->item))
        {
          $children = collect($response->item->models);
          $sorted = array_values($children->sortBy('name')->toArray());

          $response->item->sorted_models_by_name = $sorted;
        }

        return json_encode($response);
    }

    public function shopeeDetail(Request $request)
    {
      return MarketPlace::driver('shopee')->item->getItemDetail($request);
    }
}
