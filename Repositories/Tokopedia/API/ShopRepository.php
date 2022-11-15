<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\Tokopedia\API;

use Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\Tokopedia\Foundation\AbstractRepository;

use Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\Product;
use Log;

use Gdevilbat\SpardaCMS\Modules\Ecommerce\Contracts\MarketPlaceShopInterface;

/**
 * Class EloquentCoreRepository
 *
 * @package Gdevilbat\SpardaCMS\Modules\Core\Repositories\Eloquent
 */
class ShopRepository extends AbstractRepository implements MarketPlaceShopInterface
{
	/**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function getShopDetail(array $request): Object
    {
    	$this->validateRequest($request, [
        ]);

       $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://gql.tokopedia.com/",
          CURLOPT_ENCODING => "",
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_POSTFIELDS =>"[{\"operationName\":\"ShopInfoCore\",\"variables\":{\"id\":0,\"domain\":\"".$request['shop_id']."\"},\"query\":\"query ShopInfoCore(\$id: Int!, \$domain: String) {\\n  shopInfoByID(input: {shopIDs: [\$id], fields: [\\\"active_product\\\", \\\"address\\\", \\\"allow_manage\\\", \\\"assets\\\", \\\"core\\\", \\\"closed_info\\\", \\\"create_info\\\", \\\"favorite\\\", \\\"location\\\", \\\"status\\\", \\\"is_open\\\", \\\"other-goldos\\\", \\\"shipment\\\", \\\"shopstats\\\", \\\"shop-snippet\\\", \\\"other-shiploc\\\", \\\"shopHomeType\\\"], domain: \$domain, source: \\\"shoppage\\\"}) {\\n    result {\\n      shopCore {\\n        description\\n        domain\\n        shopID\\n        name\\n        tagLine\\n        defaultSort\\n        __typename\\n      }\\n      createInfo {\\n        openSince\\n        __typename\\n      }\\n      favoriteData {\\n        totalFavorite\\n        alreadyFavorited\\n        __typename\\n      }\\n      activeProduct\\n      shopAssets {\\n        avatar\\n        cover\\n        __typename\\n      }\\n      location\\n      isAllowManage\\n      isOpen\\n      shopHomeType\\n      address {\\n        name\\n        id\\n        email\\n        phone\\n        area\\n        districtName\\n        __typename\\n      }\\n      shipmentInfo {\\n        isAvailable\\n        image\\n        name\\n        product {\\n          isAvailable\\n          productName\\n          uiHidden\\n          __typename\\n        }\\n        __typename\\n      }\\n      shippingLoc {\\n        districtName\\n        cityName\\n        __typename\\n      }\\n      shopStats {\\n        productSold\\n        totalTxSuccess\\n        totalShowcase\\n        __typename\\n      }\\n      statusInfo {\\n        shopStatus\\n        statusMessage\\n        statusTitle\\n        __typename\\n      }\\n      closedInfo {\\n        closedNote\\n        until\\n        reason\\n        __typename\\n      }\\n      bbInfo {\\n        bbName\\n        bbDesc\\n        bbNameEN\\n        bbDescEN\\n        __typename\\n      }\\n      goldOS {\\n        isGold\\n        isGoldBadge\\n        isOfficial\\n        badge\\n        __typename\\n      }\\n      shopSnippetURL\\n      customSEO {\\n        title\\n        description\\n        bottomContent\\n        __typename\\n      }\\n      __typename\\n    }\\n    error {\\n      message\\n      __typename\\n    }\\n    __typename\\n  }\\n}\\n\"}]",
          CURLOPT_HTTPHEADER => array(
            "content-type: application/json"
          ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        if(empty($response))
            return response()->json(['message' => 'Check Connection'], 500);

        return json_decode($response)[0];
    }

    public function getAuthUrl($callback)
    {
        $time = \Carbon\Carbon::now()->timestamp;
        $path = '/api/v2/shop/auth_partner';
        $base_string =  config('cms-ecommerce.SHOPEE_PARTNER_ID').$path.$time;
        $sign = $this->getSignature($base_string);
        $url = url(config('cms-ecommerce.SHOPEE_API_URL').$path.'?'.http_build_query(['partner_id' => config('cms-ecommerce.SHOPEE_PARTNER_ID'), 'redirect' => $callback, 'timestamp' => $time, 'sign' => $sign]));
        return redirect($url);
    }
}
