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

    public function scrappingLazada(Request $request)
    {
      $this->validate($request, [
          'slug' => 'required',
      ]);

      $curl = curl_init();

      curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://www.lazada.co.id/'.$request->slug.'.html',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
          'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
          'Cookie: hng=ID|id-ID|IDR|360; hng.sig=to18pG508Hzz7EPB_okhuQu8kDUP3TDmLlnu4IbIOY8'
        ),
      ));

      $response = curl_exec($curl);

      curl_close($curl);

      if(empty($response))
            return response()->json(['message' => 'Check Connection'], 500);

      preg_match('/var __moduleData__ = (.+);([\s]+)var __googleBot__/i', $response, $match);

      $data = preg_replace('/(var __moduleData__ = |;(\s+)var __googleBot__$)/i', '', $match[0]);

      $data = json_decode($data);
      $data = $data->data->root->fields;

      /*==========================================
      =            Parsing Model Name            =
      ==========================================*/
      
        $skus = collect($data->productOption->skuBase->skus);

        foreach ($skus as $key_sku => $value) {
          $list_path = [];
          $path = explode(';', $value->propPath);

          foreach ($path as $key => $value) {
            $list_path[$key] = explode(':', $value);
          }

          $name = '';

          $properties = collect($data->productOption->skuBase->properties);
          foreach ($list_path as $key => $value) {
            $item = $properties->where('pid', $value[0])->first();
            $item = collect($item->values)->where('vid', $value[1])->first();
            $name .= $item->name.', ';
          }

          $data->productOption->skuBase->skus[$key_sku]->name = trim($name);
        }
      
      /*=====  End of Parsing Model Name  ======*/

      $sku_base = collect($data->productOption->skuBase);

      $sku_base_sort_by_id = $sku_base->map(function($item, $key){
        if($key == 'properties')
        {
          $item = array_values(collect($item)->sortBy('pid')->toArray());
        }
        if($key == 'skus')
        {
          $item = array_values(collect($item)->sortBy('skuId')->toArray());
        }

        return $item;
      });

      $sku_base_sort_by_name = $sku_base->map(function($item, $key){
        if($key == 'properties')
        {
          $item = array_values(collect($item)->sortBy('pid')->toArray());
        }
        if($key == 'skus')
        {
          $item = array_values(collect($item)->sortBy('name')->toArray());
        }

        return $item;
      });


      $data->productOption->skuBaseSortById = $sku_base_sort_by_id->toArray();
      $data->productOption->skuBaseSortByName = $sku_base_sort_by_name->toArray();

      $sku_info = collect($data->skuInfos);

      foreach ($sku_info as $key => $value) {
        $item = collect($data->productOption->skuBaseSortByName['skus'])->where('skuId', $value->skuId)->first();
        $data->skuInfos->$key->name = $item->name;
      }

      $sku_info = collect($data->skuInfos)->unique('skuId');

      $sku_info_sort_by_id = array_values($sku_info->sortBy('skuId')->toArray());
      $sku_info_sort_by_name = array_values($sku_info->sortBy('name')->toArray());

      $data->skuInfosSortById = $sku_info_sort_by_id;
      $data->skuInfosSortByName = $sku_info_sort_by_name;

      return response()->json($data);
    }
}
