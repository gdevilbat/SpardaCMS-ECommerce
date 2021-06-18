<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class ScrappingController extends Controller
{
    public function scrappingProduct(Request $request)
    {
        $this->validate($request, [
          'domain' => 'required',
          'productKey' => 'required'
        ]);

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://gql.tokopedia.com/",
          CURLOPT_ENCODING => "",
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_POSTFIELDS =>"[{\"operationName\":\"PDPInfoQuery\",\"variables\":{\"shopDomain\":\"".$request->domain."\",\"productKey\":\"".$request->productKey."\"},\"query\":\"query PDPInfoQuery(\$shopDomain: String, \$productKey: String) {\\n  getPDPInfo(productID: 0, shopDomain: \$shopDomain, productKey: \$productKey) {\\n    basic {\\n      id\\n      shopID\\n      name\\n      alias\\n      price\\n      priceCurrency\\n      lastUpdatePrice\\n      description\\n      minOrder\\n      maxOrder\\n      status\\n      weight\\n      weightUnit\\n      condition\\n      url\\n      sku\\n      gtin\\n      isKreasiLokal\\n      isMustInsurance\\n      isEligibleCOD\\n      isLeasing\\n      catalogID\\n      needPrescription\\n      __typename\\n    }\\n    category {\\n      id\\n      name\\n      title\\n      breadcrumbURL\\n      isAdult\\n      detail {\\n        id\\n        name\\n        breadcrumbURL\\n        __typename\\n      }\\n      __typename\\n    }\\n    pictures {\\n      picID\\n      fileName\\n      filePath\\n      description\\n      isFromIG\\n      width\\n      height\\n      urlOriginal\\n      urlThumbnail\\n      url300\\n      status\\n      __typename\\n    }\\n    preorder {\\n      isActive\\n      duration\\n      timeUnit\\n      __typename\\n    }\\n    wholesale {\\n      minQty\\n      price\\n      __typename\\n    }\\n    videos {\\n      source\\n      url\\n      __typename\\n    }\\n    campaign {\\n      campaignID\\n      campaignType\\n      campaignTypeName\\n      originalPrice\\n      discountedPrice\\n      isAppsOnly\\n      isActive\\n      percentageAmount\\n      stock\\n      originalStock\\n      startDate\\n      endDate\\n      endDateUnix\\n      appLinks\\n      hideGimmick\\n      __typename\\n    }\\n    stats {\\n      countView\\n      countReview\\n      countTalk\\n      rating\\n      __typename\\n    }\\n    txStats {\\n      txSuccess\\n      txReject\\n      itemSold\\n      itemSoldPaymentVerified\\n      __typename\\n    }\\n    cashback {\\n      percentage\\n      __typename\\n    }\\n    variant {\\n      parentID\\n      isVariant\\n      __typename\\n    }\\n    stock {\\n      useStock\\n      value\\n      stockWording\\n      __typename\\n    }\\n    menu {\\n      name\\n      __typename\\n    }\\n    __typename\\n  }\\n}\\n\"}]",
          CURLOPT_HTTPHEADER => array(
            "content-type: application/json",
          ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        if(empty($response))
            return response()->json(['message' => 'Check Connection'], 500);

        return json_decode($response);
    }

    public function scrappingVariant(Request $request)
    {
        $this->validate($request, [
          'variant_id' => 'required',
        ]);

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://gql.tokopedia.com/",
          CURLOPT_ENCODING => "",
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_POSTFIELDS =>"[\r\n    {\r\n        \"operationName\": \"ProductVariantQuery\",\r\n        \"variables\": {\r\n            \"productID\": \"".$request->variant_id."\",\r\n            \"includeCampaign\": true\r\n        },\r\n        \"query\": \"query ProductVariantQuery(\$productID: String!, \$includeCampaign: Boolean!) {\\n  getProductVariant(productID: \$productID, option: {userID: \\\"0\\\", includeCampaign: \$includeCampaign}) {\\n    parentID\\n    defaultChild\\n    variant {\\n      productVariantID\\n      variantID\\n      variantUnitID\\n      name\\n      identifier\\n      unitName\\n      position\\n      option {\\n        productVariantOptionID\\n        variantUnitValueID\\n        value\\n        hex\\n        picture {\\n          urlOriginal: url\\n          urlThumbnail: url200\\n          __typename\\n        }\\n        __typename\\n      }\\n      __typename\\n    }\\n    children {\\n      productID\\n      price\\n      priceFmt\\n      sku\\n      optionID\\n      productName\\n      productURL\\n      picture {\\n        urlOriginal: url\\n        urlThumbnail: url200\\n        __typename\\n      }\\n      stock {\\n        stock\\n        isBuyable\\n        alwaysAvailable\\n        isLimitedStock\\n        stockWording\\n        stockWordingHTML\\n        otherVariantStock\\n        minimumOrder\\n        maximumOrder\\n        __typename\\n      }\\n      isCOD\\n      isWishlist\\n      campaignInfo {\\n        stock\\n        originalStock\\n        endDateUnix\\n        isActive\\n        appLinks\\n        startDate\\n        campaignID\\n        isAppsOnly\\n        campaignType\\n        originalPrice\\n        discountPrice\\n        originalPriceFmt\\n        discountPriceFmt\\n        campaignTypeName\\n        discountPercentage\\n        hideGimmick\\n        __typename\\n      }\\n      __typename\\n    }\\n    sizeChart\\n    enabled\\n    alwaysAvailable\\n    stock\\n    __typename\\n  }\\n}\\n\"\r\n    }\r\n]",
          CURLOPT_HTTPHEADER => array(
            "content-type: application/json",
          ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        if(empty($response))
            return response()->json(['message' => 'Check Connection'], 500);

        return json_decode($response);
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
              'if-none-match-: 55b03-31e022ef540232a0b96aa571cff8f335',
              'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36',
              'if-none-match: c53b2662fc864184c7e15e3aaa69c196'
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
