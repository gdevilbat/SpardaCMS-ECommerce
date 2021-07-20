<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\Tokopedia\API;

use Gdevilbat\SpardaCMS\Modules\Ecommerce\Contracts\MarketPlaceItemInterface;
use Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\Tokopedia\Foundation\AbstractRepository;

use Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\Product;
use Log;

use MarketPlace;

/**
 * Class EloquentCoreRepository
 *
 * @package Gdevilbat\SpardaCMS\Modules\Core\Repositories\Eloquent
 */
class ItemRepository extends AbstractRepository implements MarketPlaceItemInterface
{
	public function getItemsList(array $request): Object
    {
    	$this->validateRequest($request, [
            'page' => 'required|min:1'
        ]);

        $page = $request['page'] ?: 1;
        $limit = !empty($request['limit']) ? $request['limit'] : 80;

        $id = MarketPlace::driver('tokopedia')->shop->getShopDetail($request)->data->shopInfoByID->result[0]->shopCore->shopID;

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://ace.tokopedia.com/v1/web-service/shop/get_shop_product?etalase=etalase&order_by=9&page=".$request['page']."&per_page=".$limit."&shop_id=".$id,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
            'cache-control: max-age=0',
            'accept-language: en-US,en;q=0.9',
            'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36'
          ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        if(empty($response))
            return response()->json(['message' => 'Check Connection'], 500);

        return json_decode($response);
    }

    public function getItemDetail(array $request): Object
    {
    	$this->validateRequest($request, [
	        'slug' => 'required'
        ]);

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://gql.tokopedia.com/",
          CURLOPT_ENCODING => "",
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_POSTFIELDS =>"[{\"operationName\":\"PDPGetLayoutQuery\",\"variables\":{\"shopDomain\":\"".$request['shop_id']."\",\"productKey\":\"".$request['slug']."\",\"layoutID\":\"\",\"apiVersion\":1},\"query\":\"fragment ProductVariant on pdpDataProductVariant {\\n  errorCode\\n  parentID\\n  defaultChild\\n  sizeChart\\n  variants {\\n    productVariantID\\n    variantID\\n    name\\n    identifier\\n    option {\\n      picture {\\n        urlOriginal: url\\n        urlThumbnail: url100\\n        __typename\\n      }\\n      productVariantOptionID\\n      variantUnitValueID\\n      value\\n      hex\\n      __typename\\n    }\\n    __typename\\n  }\\n  children {\\n    productID\\n    price\\n    priceFmt\\n    optionID\\n    productName\\n    productURL\\n    picture {\\n      urlOriginal: url\\n      urlThumbnail: url100\\n      __typename\\n    }\\n    stock {\\n      stock\\n      isBuyable\\n      stockWording\\n      stockWordingHTML\\n      minimumOrder\\n      maximumOrder\\n      __typename\\n    }\\n    isCOD\\n    isWishlist\\n    campaignInfo {\\n      campaignID\\n      campaignType\\n      campaignTypeName\\n      campaignIdentifier\\n      background\\n      discountPercentage\\n      originalPrice\\n      discountPrice\\n      stock\\n      stockSoldPercentage\\n      threshold\\n      startDate\\n      endDate\\n      endDateUnix\\n      appLinks\\n      isAppsOnly\\n      isActive\\n      hideGimmick\\n      isCheckImei\\n      __typename\\n    }\\n    thematicCampaign {\\n      additionalInfo\\n      background\\n      campaignName\\n      icon\\n      __typename\\n    }\\n    __typename\\n  }\\n  __typename\\n}\\n\\nfragment ProductMedia on pdpDataProductMedia {\\n  media {\\n    type\\n    urlThumbnail: URLThumbnail\\n    videoUrl: videoURLAndroid\\n    prefix\\n    suffix\\n    description\\n    __typename\\n  }\\n  videos {\\n    source\\n    url\\n    __typename\\n  }\\n  __typename\\n}\\n\\nfragment ProductHighlight on pdpDataProductContent {\\n  name\\n  price {\\n    value\\n    currency\\n    __typename\\n  }\\n  campaign {\\n    campaignID\\n    campaignType\\n    campaignTypeName\\n    campaignIdentifier\\n    background\\n    percentageAmount\\n    originalPrice\\n    discountedPrice\\n    originalStock\\n    stock\\n    stockSoldPercentage\\n    threshold\\n    startDate\\n    endDate\\n    endDateUnix\\n    appLinks\\n    isAppsOnly\\n    isActive\\n    hideGimmick\\n    __typename\\n  }\\n  thematicCampaign {\\n    additionalInfo\\n    background\\n    campaignName\\n    icon\\n    __typename\\n  }\\n  stock {\\n    useStock\\n    value\\n    stockWording\\n    __typename\\n  }\\n  variant {\\n    isVariant\\n    parentID\\n    __typename\\n  }\\n  wholesale {\\n    minQty\\n    price {\\n      value\\n      currency\\n      __typename\\n    }\\n    __typename\\n  }\\n  isCashback {\\n    percentage\\n    __typename\\n  }\\n  isTradeIn\\n  isOS\\n  isPowerMerchant\\n  isWishlist\\n  isCOD\\n  isFreeOngkir {\\n    isActive\\n    __typename\\n  }\\n  preorder {\\n    duration\\n    timeUnit\\n    isActive\\n    preorderInDays\\n    __typename\\n  }\\n  __typename\\n}\\n\\nfragment ProductCustomInfo on pdpDataCustomInfo {\\n  icon\\n  title\\n  isApplink\\n  applink\\n  separator\\n  description\\n  __typename\\n}\\n\\nfragment ProductInfo on pdpDataProductInfo {\\n  row\\n  content {\\n    title\\n    subtitle\\n    applink\\n    __typename\\n  }\\n  __typename\\n}\\n\\nfragment ProductDetail on pdpDataProductDetail {\\n  content {\\n    title\\n    subtitle\\n    applink\\n    showAtFront\\n    isAnnotation\\n    __typename\\n  }\\n  __typename\\n}\\n\\nfragment ProductDataInfo on pdpDataInfo {\\n  icon\\n  title\\n  isApplink\\n  applink\\n  content {\\n    icon\\n    text\\n    __typename\\n  }\\n  __typename\\n}\\n\\nfragment ProductSocial on pdpDataSocialProof {\\n  row\\n  content {\\n    icon\\n    title\\n    subtitle\\n    applink\\n    type\\n    rating\\n    __typename\\n  }\\n  __typename\\n}\\n\\nquery PDPGetLayoutQuery(\$shopDomain: String, \$productKey: String, \$layoutID: String, \$apiVersion: Float) {\\n  pdpGetLayout(shopDomain: \$shopDomain, productKey: \$productKey, layoutID: \$layoutID, apiVersion: \$apiVersion) {\\n    name\\n    basicInfo {\\n      alias\\n      id: productID\\n      shopID\\n      shopName\\n      minOrder\\n      maxOrder\\n      weight\\n      weightUnit\\n      condition\\n      status\\n      url\\n      needPrescription\\n      catalogID\\n      isLeasing\\n      isBlacklisted\\n      menu {\\n        id\\n        name\\n        url\\n        __typename\\n      }\\n      category {\\n        id\\n        name\\n        title\\n        breadcrumbURL\\n        isAdult\\n        detail {\\n          id\\n          name\\n          breadcrumbURL\\n          isAdult\\n          __typename\\n        }\\n        __typename\\n      }\\n      blacklistMessage {\\n        title\\n        description\\n        button\\n        url\\n        __typename\\n      }\\n      txStats {\\n        transactionSuccess\\n        transactionReject\\n        countSold\\n        paymentVerified\\n        itemSoldPaymentVerified\\n        __typename\\n      }\\n      stats {\\n        countView\\n        countReview\\n        countTalk\\n        rating\\n        __typename\\n      }\\n      __typename\\n    }\\n    components {\\n      name\\n      type\\n      position\\n      data {\\n        ...ProductMedia\\n        ...ProductHighlight\\n        ...ProductInfo\\n        ...ProductDetail\\n        ...ProductSocial\\n        ...ProductDataInfo\\n        ...ProductCustomInfo\\n        ...ProductVariant\\n        __typename\\n      }\\n      __typename\\n    }\\n    __typename\\n  }\\n}\\n\"}]",
          CURLOPT_HTTPHEADER => array(
            "content-type: application/json",
            'x-device: desktop',
            'x-tkpd-akamai: pdpGetLayout',
          ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        if(empty($response))
            return response()->json(['message' => 'Check Connection'], 500);

        $response = json_decode($response);

        if(empty($response[0]->errors))
        {
          if($response[0]->data->pdpGetLayout->components[3]->data[0]->variant->isVariant)
          {
            $parent_name = $response[0]->data->pdpGetLayout->components[3]->data[0]->name;
            $children = collect($response[0]->data->pdpGetLayout->components[2]->data[0]->children);

            $new_children = $children->map(function($item, $key) use ($parent_name){
              $item->name = str_replace($parent_name.' - ', '', $item->productName);

              if(!$item->stock->isBuyable)
                $item->stock->stock = 0;

              return $item;
            });

            $sorted_children_by_option_id = $new_children->sortBy('optionID');
            $sorted_children_by_id = $new_children->sortBy('productID');
            $sorted_children_by_name = $new_children->sortBy('productName');

            $sorted_children_by_option_id = array_values($sorted_children_by_option_id->toArray());
            $sorted_children_by_id = array_values($sorted_children_by_id->toArray());
            $sorted_children_by_name = array_values($sorted_children_by_name->toArray());

            $response[0]->data->pdpGetLayout->components[2]->data[0]->children = $sorted_children_by_id;
            $response[0]->data->pdpGetLayout->components[2]->data[0]->sorted_children_by_name = $sorted_children_by_name;
            $response[0]->data->pdpGetLayout->components[2]->data[0]->sorted_children_by_option_id = $sorted_children_by_option_id;

            foreach ($response[0]->data->pdpGetLayout->components[2]->data[0]->variants as $key => $variant) {
              $option = collect($variant->option);
              $sorted_option_by_id = $option->sortBy('productVariantOptionID');
              $sorted_option_by_id = array_values($sorted_option_by_id->toArray());
              $response[0]->data->pdpGetLayout->components[2]->data[0]->variants[$key]->option = $sorted_option_by_id;
            }
          }
        }

        return json_decode(json_encode($response[0]));
    }

    public function getVariations(array $request): Object
    {
        $request->validate([
            'variant_id' => 'required',
        ]);
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://gql.tokopedia.com/",
          CURLOPT_ENCODING => "",
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_POSTFIELDS =>"[\r\n    {\r\n        \"operationName\": \"ProductVariantQuery\",\r\n        \"variables\": {\r\n            \"productID\": \"".$request['variant_id']."\",\r\n            \"includeCampaign\": true\r\n        },\r\n        \"query\": \"query ProductVariantQuery(\$productID: String!, \$includeCampaign: Boolean!) {\\n  getProductVariant(productID: \$productID, option: {userID: \\\"0\\\", includeCampaign: \$includeCampaign}) {\\n    parentID\\n    defaultChild\\n    variant {\\n      productVariantID\\n      variantID\\n      variantUnitID\\n      name\\n      identifier\\n      unitName\\n      position\\n      option {\\n        productVariantOptionID\\n        variantUnitValueID\\n        value\\n        hex\\n        picture {\\n          urlOriginal: url\\n          urlThumbnail: url200\\n          __typename\\n        }\\n        __typename\\n      }\\n      __typename\\n    }\\n    children {\\n      productID\\n      price\\n      priceFmt\\n      sku\\n      optionID\\n      productName\\n      productURL\\n      picture {\\n        urlOriginal: url\\n        urlThumbnail: url200\\n        __typename\\n      }\\n      stock {\\n        stock\\n        isBuyable\\n        alwaysAvailable\\n        isLimitedStock\\n        stockWording\\n        stockWordingHTML\\n        otherVariantStock\\n        minimumOrder\\n        maximumOrder\\n        __typename\\n      }\\n      isCOD\\n      isWishlist\\n      campaignInfo {\\n        stock\\n        originalStock\\n        endDateUnix\\n        isActive\\n        appLinks\\n        startDate\\n        campaignID\\n        isAppsOnly\\n        campaignType\\n        originalPrice\\n        discountPrice\\n        originalPriceFmt\\n        discountPriceFmt\\n        campaignTypeName\\n        discountPercentage\\n        hideGimmick\\n        __typename\\n      }\\n      __typename\\n    }\\n    sizeChart\\n    enabled\\n    alwaysAvailable\\n    stock\\n    __typename\\n  }\\n}\\n\"\r\n    }\r\n]",
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

    public function addItem(array $request): Object
    {
        $this->validateRequest($request, [
            'name' => 'required',
            'category_id' => 'required',
            'description' => 'required',
            'price' => 'required',
            'stock' => 'required',
            'logistics' => 'required',
            'weight' => 'required',
            'images' => 'required',
            'attributes' => 'required',
            'condition' => 'required',
        ]);

        $path = '/api/v1/item/adds';
        $parameter = $this->getPrimaryParameter($request['shop_id']);
        $parameter['name'] = $request['name'];
        $parameter['category_id'] = $request['category_id'];
        $parameter['description'] = $request['description'];
        $parameter['price'] = $request['price'];
        $parameter['stock'] = $request['stock'];
        $parameter['logistics'] = $request['logistics'];
        $parameter['weight'] = $request['weight'];
        $parameter['images'] = $request['images'];
        $parameter['attributes'] = $request['attributes'];
        $parameter['condition'] = $request['condition'];

        if(array_key_exists('is_pre_order', $request))
        {
            $parameter['days_to_ship'] = $request['days_to_ship'];
            $parameter['is_pre_order'] = $request['is_pre_order'];
        }

        $base_string = $this->getBaseString($path, $parameter);
        $sign = $this->getSignature($base_string);

        $res = $this->makeRequest($path, $parameter, $sign);

        $body = $res->getBody();

        if(empty($body))
            return response()->json(['message' => 'Check Connection'], 500);

        $data = json_decode($body);

        return $data;
    }

    public function itemUpdate(array $request): Object
    {
        $this->validateRequest($request, [
          'item_id' => 'required',
          'name' => 'required',
          'description' => 'required',
        ]);

        /*========================================
        =            Update Item Info            =
        ========================================*/
        
            $path = '/api/v1/item/update';
            $parameter = $this->getPrimaryParameter($request['shop_id']);
            $parameter['item_id'] = (int) $request['item_id'];
            $parameter['name'] = $request['name'];
            $parameter['description'] = $request['description'];

            $base_string = $this->getBaseString($path, $parameter);
            $sign = $this->getSignature($base_string);

            $res = $this->makeRequest($path, $parameter, $sign);

            $body = $res->getBody();

            if(empty($body))
                return response()->json(['message' => 'Check Connection'], 500);

            $data = json_decode($body);

        /*=====  End of Update Item Info  ======*/

        return $data;;
    }

    public function itemUpdatePrice(array $request): Object
    {
        $this->validateRequest($request, [
          'item_id' => 'required',
          'price' => 'required',
        ]);

        $path = '/api/v1/items/update_price';
        $parameter = $this->getPrimaryParameter($request['shop_id']);
        $parameter['item_id'] = (int) $request['item_id'];
        $parameter['price'] = (int ) $request['price'];

        $base_string = $this->getBaseString($path, $parameter);
        $sign = $this->getSignature($base_string);

        $res = $this->makeRequest($path, $parameter, $sign);

        $body = $res->getBody();

        if(empty($body))
            return response()->json(['message' => 'Check Connection'], 500);

        $data = json_decode($body);

        return $data;
    }

    public function itemUpdateStock(array $request): Object
    {
        $this->validateRequest($request, [
          'item_id' => 'required',
          'stock' => 'required',
        ]);

        $path = '/api/v1/items/update_stock';
        $parameter = $this->getPrimaryParameter($request['shop_id']);
        $parameter['item_id'] = (int) $request['item_id'];
        $parameter['stock'] = (int) $request['stock'];

        $base_string = $this->getBaseString($path, $parameter);
        $sign = $this->getSignature($base_string);

        $res = $this->makeRequest($path, $parameter, $sign);

        $body = $res->getBody();

        if(empty($body))
            return response()->json(['message' => 'Check Connection'], 500);

        $data = json_decode($body);

        return $data;
    }

    public function itemInitTierVariations(array $request): Object
    {
        $this->validateRequest($request, [
          'item_id' => 'required',
          'tier_variation' => 'required|array',
          'tier_variation.*.name' => 'required',
          'tier_variation.*.options' => 'required|array',
          'tier_variation.*.options.*' => 'required',
          'variation' => 'required|array',
          'variation.*.tier_index' => 'required|array',
          'variation.*.stock' => 'required',
          'variation.*.price' => 'required'
        ]);

        $path = '/api/v1/item/tier_var/init';
        $parameter = $this->getPrimaryParameter($request['shop_id']);
        $parameter['item_id'] = $request['item_id'];
        $parameter['tier_variation'] = $request['tier_variation'];
        $parameter['variation'] = $request['variation'];
        $base_string = $this->getBaseString($path, $parameter);
        $sign = $this->getSignature($base_string);

        $res = $this->makeRequest($path, $parameter, $sign);

        $body = $res->getBody();

        if(empty($body))
            return response()->json(['message' => 'Check Connection'], 500);

        $data = json_decode($body);

        return $data;
    }

    public function itemAddTierVariations(array $request): Object
    {
        $this->validateRequest($request, [
          'item_id' => 'required',
          'variation' => 'required|array',
          'variation.*.tier_index' => 'required|array',
          'variation.*.stock' => 'required',
          'variation.*.price' => 'required'
        ]);

        $path = '/api/v1/item/tier_var/add';
        $parameter = $this->getPrimaryParameter($request['shop_id']);
        $parameter['item_id'] = $request['item_id'];
        $parameter['variation'] = $request['variation'];
        $base_string = $this->getBaseString($path, $parameter);
        $sign = $this->getSignature($base_string);

        $res = $this->makeRequest($path, $parameter, $sign);

        $body = $res->getBody();

        if(empty($body))
            return response()->json(['message' => 'Check Connection'], 500);

        $data = json_decode($body);

        return $data;
    }

    public function itemUpdateTierVariationList(array $request): Object
    {
        $this->validateRequest($request, [
          'item_id' => 'required',
          'tier_variation' => 'required|array',
          'tier_variation.*.name' => 'required',
          'tier_variation.*.options' => 'required|array',
          'tier_variation.*.options.*' => 'required',
        ]);

        $path = '/api/v1/item/tier_var/update_list';
        $parameter = $this->getPrimaryParameter($request['shop_id']);
        $parameter['item_id'] = $request['item_id'];
        $parameter['tier_variation'] = $request['tier_variation'];
        $base_string = $this->getBaseString($path, $parameter);
        $sign = $this->getSignature($base_string);

        $res = $this->makeRequest($path, $parameter, $sign);

        $body = $res->getBody();

        if(empty($body))
            return response()->json(['message' => 'Check Connection'], 500);

        $data = json_decode($body);

        return $data;
    }

    public function itemUpdateTierVariationIndex(array $request): Object
    {
        $this->validateRequest($request, [
          'item_id' => 'required',
          'variation' => 'required|array',
          'variation.*.tier_index' => 'required|array',
          'variation.*.variation_id' => 'required',
        ]);

        $path = '/api/v1/item/tier_var/update';
        $parameter = $this->getPrimaryParameter($request['shop_id']);
        $parameter['item_id'] = $request['item_id'];
        $parameter['variation'] = $request['variation'];
        $base_string = $this->getBaseString($path, $parameter);
        $sign = $this->getSignature($base_string);

        $res = $this->makeRequest($path, $parameter, $sign);

        $body = $res->getBody();

        if(empty($body))
            return response()->json(['message' => 'Check Connection'], 500);

        $data = json_decode($body);

        return $data;
    }

    public function itemUpdateVariationPriceBatch(array $request): Object
    {
        $this->validateRequest($request, [
          'variations' => 'required|array',
          'variations.*.price' => 'required',
          'variations.*.variation_id' => 'required',
          'variations.*.item_id' => 'required'
        ]);

        $path = '/api/v1/items/update/vars_price';
        $parameter = $this->getPrimaryParameter($request['shop_id']);
        $parameter['variations'] = $request['variations'];
        $base_string = $this->getBaseString($path, $parameter);
        $sign = $this->getSignature($base_string);

        $res = $this->makeRequest($path, $parameter, $sign);

        $body = $res->getBody();

        if(empty($body))
            return response()->json(['message' => 'Check Connection'], 500);

        $data = json_decode($body);

        return $data;
    }

    public function itemUpdateVariationStockBatch(array $request): Object
    {
        $this->validateRequest($request, [
          'variations' => 'required|array',
          'variations.*.stock' => 'required',
          'variations.*.variation_id' => 'required',
          'variations.*.item_id' => 'required'
        ]);

        $path = '/api/v1/items/update/vars_stock';
        $parameter = $this->getPrimaryParameter($request['shop_id']);
        $parameter['variations'] = $request['variations'];
        $base_string = $this->getBaseString($path, $parameter);
        $sign = $this->getSignature($base_string);

        $res = $this->makeRequest($path, $parameter, $sign);

        $body = $res->getBody();

        if(empty($body))
            return response()->json(['message' => 'Check Connection'], 500);

        $data = json_decode($body);

        return $data;
    }

    public function getBoostedItem(array $request): Object
    {
    	$this->validateRequest($request, [
        ]);

        $path = '/api/v1/items/get_boosted';
        $parameter = $this->getPrimaryParameter($request['shop_id']);

        $base_string = $this->getBaseString($path, $parameter);
        $sign = $this->getSignature($base_string);

        $res = $this->makeRequest($path, $parameter, $sign);

        $body = $res->getBody();

        if(empty($body))
            return response()->json(['message' => 'Check Connection'], 500);

        $data = json_decode($body);

        try {
            $items = $data->items;
        } catch (\ErrorException $e) {
            log::info(json_encode($data));
            return response()->json(['message' => $data], 500);
        }

        return response()->json($items);
    }

    public function setBoostedItem(array $request): Object
    {
    	$this->validateRequest($request, [
            'item_id' => 'required|array'
        ]);

        $path = '/api/v1/items/boost';
        $parameter = $this->getPrimaryParameter($request['shop_id']);
        $parameter['item_id'] = $request['item_id'];

        $base_string = $this->getBaseString($path, $parameter);
        $sign = $this->getSignature($base_string);

        $res = $this->makeRequest($path, $parameter, $sign);

        $body = $res->getBody();

        if(empty($body))
            return response()->json(['message' => 'Check Connection'], 500);

        $data = json_decode($body);

        return $data;
    }

    public function getCategories(array $request): Object
    {
        $this->validateRequest($request, [
            'language' => 'required|in:en,vi,th,zh-Hant,zh-Hans,ms-my,pt-br,id'
        ]);

        $path = '/api/v1/item/categories/get';
        $parameter = $this->getPrimaryParameter($request['shop_id']);
        $parameter['language'] = $request['language'];

        $base_string = $this->getBaseString($path, $parameter);
        $sign = $this->getSignature($base_string);

        $res = $this->makeRequest($path, $parameter, $sign);

        $body = $res->getBody();

        if(empty($body))
            return response()->json(['message' => 'Check Connection'], 500);

        $data = json_decode($body);

        return $data;
    }

    public function getAttributes(array $request): Object
    {
        $this->validateRequest($request, [
            'language' => 'required|in:en,vi,th,zh-Hant,zh-Hans,ms-my,pt-br,id',
            'category_id' => 'required'
        ]);

        $path = '/api/v1/item/attributes/get';
        $parameter = $this->getPrimaryParameter($request['shop_id']);
        $parameter['language'] = $request['language'];
        $parameter['category_id'] = (integer) $request['category_id'];

        $base_string = $this->getBaseString($path, $parameter);
        $sign = $this->getSignature($base_string);

        $res = $this->makeRequest($path, $parameter, $sign);

        $body = $res->getBody();

        if(empty($body))
            return response()->json(['message' => 'Check Connection'], 500);

        $data = json_decode($body);

        return $data;
    }
}
