<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => 'core.auth'], function() {

	Route::group(['prefix' => 'control'], function() {
	    
		Route::group(['prefix' => 'product'], function() {

	        /*=============================================
	        =            Product CMS            =
	        =============================================*/

	        	Route::group(['middleware' => 'core.menu'], function() {
				    Route::get('master', 'ProductController@index')->middleware('can:menu-ecommerce')->name('product');
				    Route::get('form', 'ProductController@create')->name('product');
				    Route::post('form', 'ProductController@store')->middleware('can:create-ecommerce')->name('product');
				    Route::put('form', 'ProductController@store')->name('product');
				    Route::delete('form', 'ProductController@destroy')->name('product');
				});

			    Route::group(['prefix' => 'api'], function() {
				    Route::get('master', 'ProductController@serviceMaster')->middleware('can:menu-ecommerce');
			    });

			    Route::group(['prefix' => 'shopee'], function() {
				    Route::get('authentication', 'ShopeeController@authentication');
					Route::get('callback', 'ShopeeController@callback');
					Route::post('item-scheduled', 'ShopeeController@saveItemScheduled');
					Route::get('shopee-promotion', 'ShopeeController@shopeePromotion')->middleware('core.menu')->name('marketplace-shopee');
					Route::group(['prefix' => 'api'], function() {
					    Route::get('master', 'ShopeeController@serviceMaster')->middleware('can:menu-ecommerce');
				    });
			    });
	        
	        /*=====  End of Product CMS  ======*/

		});

		Route::group(['prefix' => 'product-category'], function() {

	        /*=============================================
	        =            Category CMS            =
	        =============================================*/

	        	Route::group(['middleware' => 'core.menu'], function() {
				    Route::get('master', 'CategoryController@index')->middleware('can:menu-ecommerce')->name('product-category');
				    Route::get('form', 'CategoryController@create')->name('product-category');
				    Route::post('form', 'CategoryController@store')->middleware('can:create-ecommerce')->name('product-category');
				    Route::put('form', 'CategoryController@store')->name('product-category');
				    Route::delete('form', 'CategoryController@destroy')->name('product-category');
				});
	        

			    Route::group(['prefix' => 'api'], function() {
				    Route::get('master', 'CategoryController@serviceMaster')->middleware('can:menu-ecommerce');
			    });
	        
	        /*=====  End of Category CMS  ======*/

		});

		Route::group(['prefix' => 'product-tag'], function() {

	        /*=============================================
	        =            Tag CMS            =
	        =============================================*/

		        Route::group(['middleware' => 'core.menu'], function() {
				    Route::get('master', 'TagController@index')->middleware('can:menu-ecommerce')->name('product-tag');
				    Route::get('form', 'TagController@create')->name('product-tag');
				    Route::post('form', 'TagController@store')->middleware('can:create-ecommerce')->name('product-tag');
				    Route::put('form', 'TagController@store')->name('product-tag');
				    Route::delete('form', 'TagController@destroy')->name('product-tag');
				});
	        

			    Route::group(['prefix' => 'api'], function() {
				    Route::get('master', 'TagController@serviceMaster')->middleware('can:menu-ecommerce');
			    });
	        
	        /*=====  End of Tag CMS  ======*/

		});

	});

});


Route::get('sitemap/ecommerce.xml', 'SitemapController@index');

Route::group(['middleware' => 'appearance.navbars'], function() {

	/*====================================
	=            Show Product            =
	====================================*/
	
		Route::get('product-category/{slug}', 'BlogProductController@taxonomyPost')->where('slug','[0-9A-Za-z-/]+');
		Route::get('product/{slug}', 'BlogProductController@show');
	
	/*=====  End of Show Product  ======*/
	
});

Route::get('ecommerce/test', function() {
    $curl = curl_init();

	curl_setopt_array($curl, array(
          CURLOPT_URL => "https://gql.tokopedia.com/",
          CURLOPT_ENCODING => "",
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_POSTFIELDS =>"[{\"operationName\":\"PDPGetLayoutQuery\",\"variables\":{\"shopDomain\":\"protechcom\",\"productKey\":\"laptop-acer-a314-33-intel-dualcore-n4120-4gb-256gb-ssd-14-w10\",\"layoutID\":\"\",\"apiVersion\":1,\"userLocation\":{\"addressID\":\"94884544\",\"districtID\":\"2232\",\"postalCode\":\"\",\"latlon\":\"-6.404264,106.802194\"}},\"query\":\"fragment ProductVariant on pdpDataProductVariant {\\n  errorCode\\n  parentID\\n  defaultChild\\n  sizeChart\\n  variants {\\n    productVariantID\\n    variantID\\n    name\\n    identifier\\n    option {\\n      picture {\\n        urlOriginal: url\\n        urlThumbnail: url100\\n        __typename\\n      }\\n      productVariantOptionID\\n      variantUnitValueID\\n      value\\n      hex\\n      __typename\\n    }\\n    __typename\\n  }\\n  children {\\n    productID\\n    price\\n    priceFmt\\n    optionID\\n    productName\\n    productURL\\n    picture {\\n      urlOriginal: url\\n      urlThumbnail: url100\\n      __typename\\n    }\\n    stock {\\n      stock\\n      isBuyable\\n      stockWording\\n      stockWordingHTML\\n      minimumOrder\\n      maximumOrder\\n      __typename\\n    }\\n    isCOD\\n    isWishlist\\n    campaignInfo {\\n      campaignID\\n      campaignType\\n      campaignTypeName\\n      campaignIdentifier\\n      background\\n      discountPercentage\\n      originalPrice\\n      discountPrice\\n      stock\\n      stockSoldPercentage\\n      threshold\\n      startDate\\n      endDate\\n      endDateUnix\\n      appLinks\\n      isAppsOnly\\n      isActive\\n      hideGimmick\\n      isCheckImei\\n      __typename\\n    }\\n    thematicCampaign {\\n      additionalInfo\\n      background\\n      campaignName\\n      icon\\n      __typename\\n    }\\n    __typename\\n  }\\n  __typename\\n}\\n\\nfragment ProductMedia on pdpDataProductMedia {\\n  media {\\n    type\\n    urlThumbnail: URLThumbnail\\n    videoUrl: videoURLAndroid\\n    prefix\\n    suffix\\n    description\\n    __typename\\n  }\\n  videos {\\n    source\\n    url\\n    __typename\\n  }\\n  __typename\\n}\\n\\nfragment ProductHighlight on pdpDataProductContent {\\n  name\\n  price {\\n    value\\n    currency\\n    __typename\\n  }\\n  campaign {\\n    campaignID\\n    campaignType\\n    campaignTypeName\\n    campaignIdentifier\\n    background\\n    percentageAmount\\n    originalPrice\\n    discountedPrice\\n    originalStock\\n    stock\\n    stockSoldPercentage\\n    threshold\\n    startDate\\n    endDate\\n    endDateUnix\\n    appLinks\\n    isAppsOnly\\n    isActive\\n    hideGimmick\\n    __typename\\n  }\\n  thematicCampaign {\\n    additionalInfo\\n    background\\n    campaignName\\n    icon\\n    __typename\\n  }\\n  stock {\\n    useStock\\n    value\\n    stockWording\\n    __typename\\n  }\\n  variant {\\n    isVariant\\n    parentID\\n    __typename\\n  }\\n  wholesale {\\n    minQty\\n    price {\\n      value\\n      currency\\n      __typename\\n    }\\n    __typename\\n  }\\n  isCashback {\\n    percentage\\n    __typename\\n  }\\n  isTradeIn\\n  isOS\\n  isPowerMerchant\\n  isWishlist\\n  isCOD\\n  isFreeOngkir {\\n    isActive\\n    __typename\\n  }\\n  preorder {\\n    duration\\n    timeUnit\\n    isActive\\n    preorderInDays\\n    __typename\\n  }\\n  __typename\\n}\\n\\nfragment ProductCustomInfo on pdpDataCustomInfo {\\n  icon\\n  title\\n  isApplink\\n  applink\\n  separator\\n  description\\n  __typename\\n}\\n\\nfragment ProductInfo on pdpDataProductInfo {\\n  row\\n  content {\\n    title\\n    subtitle\\n    applink\\n    __typename\\n  }\\n  __typename\\n}\\n\\nfragment ProductDetail on pdpDataProductDetail {\\n  content {\\n    title\\n    subtitle\\n    applink\\n    showAtFront\\n    isAnnotation\\n    __typename\\n  }\\n  __typename\\n}\\n\\nfragment ProductDataInfo on pdpDataInfo {\\n  icon\\n  title\\n  isApplink\\n  applink\\n  content {\\n    icon\\n    text\\n    __typename\\n  }\\n  __typename\\n}\\n\\nfragment ProductSocial on pdpDataSocialProof {\\n  row\\n  content {\\n    icon\\n    title\\n    subtitle\\n    applink\\n    type\\n    rating\\n    __typename\\n  }\\n  __typename\\n}\\n\\nquery PDPGetLayoutQuery(\$shopDomain: String, \$productKey: String, \$layoutID: String, \$apiVersion: Float, \$userLocation: pdpUserLocation!) {\\n  pdpGetLayout(shopDomain: \$shopDomain, productKey: \$productKey, layoutID: \$layoutID, apiVersion: \$apiVersion, userLocation: \$userLocation) {\\n    name\\n    pdpSession\\n    basicInfo {\\n      alias\\n      id: productID\\n      shopID\\n      shopName\\n      minOrder\\n      maxOrder\\n      weight\\n      weightUnit\\n      condition\\n      status\\n      url\\n      needPrescription\\n      catalogID\\n      isLeasing\\n      isBlacklisted\\n      menu {\\n        id\\n        name\\n        url\\n        __typename\\n      }\\n      category {\\n        id\\n        name\\n        title\\n        breadcrumbURL\\n        isAdult\\n        detail {\\n          id\\n          name\\n          breadcrumbURL\\n          isAdult\\n          __typename\\n        }\\n        __typename\\n      }\\n      blacklistMessage {\\n        title\\n        description\\n        button\\n        url\\n        __typename\\n      }\\n      txStats {\\n        transactionSuccess\\n        transactionReject\\n        countSold\\n        paymentVerified\\n        itemSoldPaymentVerified\\n        __typename\\n      }\\n      stats {\\n        countView\\n        countReview\\n        countTalk\\n        rating\\n        __typename\\n      }\\n      __typename\\n    }\\n    components {\\n      name\\n      type\\n      position\\n      data {\\n        ...ProductMedia\\n        ...ProductHighlight\\n        ...ProductInfo\\n        ...ProductDetail\\n        ...ProductSocial\\n        ...ProductDataInfo\\n        ...ProductCustomInfo\\n        ...ProductVariant\\n        __typename\\n      }\\n      __typename\\n    }\\n    __typename\\n  }\\n}\\n\"}]",
          CURLOPT_HTTPHEADER => array(
            "content-type: application/json",
            'x-tkpd-akamai: pdpGetLayout',
          ),
        ));

	$response = curl_exec($curl);

	curl_close($curl);
	dd(json_decode($response));
});