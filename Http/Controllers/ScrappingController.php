<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class ScrappingController extends Controller
{
    public function scrappingTokopediaProductDetail(Request $request)
    {
        $response = resolve(\Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\Tokopedia\TokopediaRepository::class)->itemDetail($request);

        if(!empty($response[0]->data))
        {
          if($response[0]->data->pdpGetLayout->components[3]->data[0]->variant->isVariant)
          {
            $children = collect($response[0]->data->pdpGetLayout->components[2]->data[0]->children);
            $sorted = array_values($children->sortBy('productName')->toArray());

            $response[0]->data->pdpGetLayout->components[2]->data[0]->children = $sorted;
          }
        }

        return $response;
    }

    public function scrappingTokopediaProductVariant(Request $request)
    {
        return resolve(\Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\Tokopedia\TokopediaRepository::class)->itemVariant($request);
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
              'if-none-match-: 55b03-5a87c55d1c152d9fd7a1cf2613bb0b91',
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

          $response->item->models = $sorted;
        }

        return json_encode($response);
    }

    public function shopeeDetail(Request $request)
    {
      $this->validate($request, [
          'product_id' => 'required',
        ]);

        $url = 'https://seller.shopee.co.id/api/v3/product/get_product_detail/?'.http_build_query(['product_id' => $request->product_id]);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_ENCODING => "",
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
              "cookie: fbm_957549474255294=base_domain=.shopee.co.id; cto_lwid=195b1764-0ef0-4bec-aa92-ac4a603bf9df; __utma=156485241.1231309772.1569496850.1571747440.1571747440.1; SPC_F=fEh1EGf8MiXYqPtpJU3Y4wQTKYud6itt; _gcl_au=1.1.2058148069.1591685537; _fbp=fb.2.1591685537834.1900528078; SC_DFP=L3uPlm1FDKc30axfxqaCuaC3rxO79u1I; G_ENABLED_IDPS=google; SPC_CDS=51f3ab59-1df7-4ae0-b9d8-2bce7d03ca4c; UYOMAPJWEMDGJ=; SPC_SC_SA_TK=; SPC_SC_SA_UD=; SPC_SC_TK=92229a9f904347e62e3685971bfb8964; SPC_WST=\"d+WJMbfdAZxq8o6f1ZZdUFSKsQVVfK+WLPrrKJbwUGW7HJP6BvMzLMvreoPN/5T71XzIKkv+cSdwm7rMWPOLv/QT5xuzNf7hj3gdzNg7CuW/YskfPO+S3KvHDyUMc3IdWQ1bCEgzpz3wI0cgqizcFsznxnC1HmZk4rnQZSZo+Vc=\"; SPC_SC_UD=89948237; SPC_U=89948237; SPC_EC=d+WJMbfdAZxq8o6f1ZZdUFSKsQVVfK+WLPrrKJbwUGW7HJP6BvMzLMvreoPN/5T71XzIKkv+cSdwm7rMWPOLv/QT5xuzNf7hj3gdzNg7CuW/YskfPO+S3KvHDyUMc3IdWQ1bCEgzpz3wI0cgqizcFsznxnC1HmZk4rnQZSZo+Vc=; AMP_TOKEN=%24NOT_FOUND; _gid=GA1.3.596009003.1598518667; SPC_SI=3abmbd01glvqb7yk94y6knoj6wlia9tq; _med=refer; _ga=GA1.1.1231309772.1569496850; _ga_SW6D8G0HXK=GS1.1.1598518666.5.1.1598520168.0; SPC_R_T_ID=\"qGo1kOmHqovN6BRYoVfDJfBnOcyr010+Xd4t6vVF41H7aZ49kfg4Zl4YWjKFO9pau2YxuUCORqb4EqaoT1S+sOiWAMpRqw9c6sxGy5LluII=\"; SPC_R_T_IV=\"HMPB0wAL55/voizsjYIyqQ==\""
            ),
          ));

        $response = curl_exec($curl);

        curl_close($curl);

        if(empty($response))
            return response()->json(['message' => 'Check Connection'], 500);

        return $response;
    }
}
