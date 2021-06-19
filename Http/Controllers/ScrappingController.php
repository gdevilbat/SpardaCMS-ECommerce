<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class ScrappingController extends Controller
{
    public function scrappingTokopediaProductDetail(Request $request)
    {
        return resolve(\Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\Tokopedia\TokopediaRepository::class)->itemDetail($request);
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
              'cookie: SC_DFP=KNjXZaLWPXH0CJYngEKpFxrq8skAO79y; _gcl_au=1.1.1484126669.1620752905; SPC_F=b9b4K3w7fxMeDmHxlehsld8usRDNaHWw; REC_T_ID=7efbfbb4-b27b-11eb-8442-ccbbfe23617b; _fbp=fb.2.1620752906430.967219403; _med=refer; G_ENABLED_IDPS=google; SPC_PC_HYBRID_ID=85; csrftoken=f446fw0Dcptm5DHM9LaE51vgl6xhZHWz; SPC_SI=bfftocid3.Y0QT7fJ2IS6LhiyHFcfnBAqSx5EcB6Zu; welcomePkgShown=true; _gid=GA1.3.1531342226.1623929990; G_AUTHUSER_H=0; SPC_U=89948237; SPC_CLIENTID=YjliNEszdzdmeE1lnpleqgvbnexchuld; SPC_EC=uSzJ+esKi6zWn3F7r/bWY/zE4tadCwin0B4phdigbYqtdPie2W4x/k5m/WF00+Ikx/HkN4KsgGNrjXiX4AyaxCB2v9y/g7GvIeMDOV4IBLnYeslEgSXXSMmxmNwjfEBOjvVjzRh6CzgCvBtBwMsfqg==; SPC_IA=1; SPC_SC_TK=2dfd553390adb3f8fc721a707490fcef; SPC_STK="bN7Gy3Pr4XO/1J6igIDQ8oahgwb/Y12GswepSxlOphNgw4LlyQ3P/Vfxh6derJQXGSFqtw3hp6ZmzMTVIPYUSqHj2Bf8An7BkiUP4hi3keLLYKl/NbxGbnTN1zOzBnpz+UftUHEbUuZptGOX4r0u677Ym46Su7eTkrY4IxsikaU="; SPC_SC_UD=89948237; _ga=GA1.1.1460827755.1620752906; SPC_R_T_ID="yzz7rTlJfzIxAIKbQe3YY05nHEmq0e+hEuAOA6KFQ834nycLvMoTP8ksQxAp5WO/uAzURPRdHa+Ys6qpII7srNkK07NPj27JeeQULhATFXY="; SPC_T_IV="bOZmuniHX13r8LIzzwRB8Q=="; SPC_R_T_IV="bOZmuniHX13r8LIzzwRB8Q=="; SPC_T_ID="yzz7rTlJfzIxAIKbQe3YY05nHEmq0e+hEuAOA6KFQ834nycLvMoTP8ksQxAp5WO/uAzURPRdHa+Ys6qpII7srNkK07NPj27JeeQULhATFXY="; _ga_SW6D8G0HXK=GS1.1.1624010165.10.0.1624010165.60',
              'if-none-match: 8d46d468b7a3ed265ad862278b8a4f79'
            ),
          ));

        $response = curl_exec($curl);

        curl_close($curl);

        if(empty($response))
            return response()->json(['message' => 'Check Connection'], 500);

        return $response;
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
