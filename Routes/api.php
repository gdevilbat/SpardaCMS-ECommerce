<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'ecommerce'], function() {
	Route::post('product-detail', 'ProductController@apiProductDetail');
	
    Route::post('scrapping-tokopedia-product-detail', 'ScrappingController@scrappingTokopediaProductDetail');
    Route::post('scrapping-tokopedia-product-variant', 'ScrappingController@scrappingTokopediaProductVariant');
	Route::post('scrapping-shopee', 'ScrappingController@scrappingShopee');
	Route::post('scrapping-lazada', 'ScrappingController@scrappingLazada');
	Route::post('get-shopee-detail', 'ScrappingController@shopeeDetail');

	Route::group(['prefix' => 'shopee'], function() {
		Route::group(['namespace' => '\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Micro'], function() {
			Route::group(['middleware' => ['auth:api','throttle:rate_limit,1']], function() {
				Route::post('shop-detail', 'ShopeeController@shopGetDetail');
				Route::post('item-detail', 'ShopeeController@itemGetDetail');
				Route::post('item-list', 'ShopeeController@itemGetList');
				Route::post('item-add', 'ShopeeController@itemAdd');
				Route::post('item-update', 'ShopeeController@itemUpdate');
				Route::post('item-boosted', 'ShopeeController@itemGetBoosted');
				Route::get('item-categories', 'ShopeeController@itemGetCategories');
				Route::get('item-attributes', 'ShopeeController@itemGetAttributes');
				Route::get('logistics', 'ShopeeController@getLogistics');
			});
		});

		Route::get('item-publish-promotion', 'ShopeeController@publishItemPromotion');
	});

	Route::group(['prefix' => 'lazada'], function() {
		Route::group(['namespace' => '\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Micro'], function() {
			Route::group(['middleware' => ['auth:api','throttle:rate_limit,1']], function() {
				Route::post('shop-detail', 'LazadaController@shopGetDetail');
				Route::post('item-detail', 'LazadaController@itemGetDetail');
				Route::post('item-list', 'LazadaController@itemGetList');
				Route::post('item-add', 'LazadaController@itemAdd');
				Route::post('item-update', 'LazadaController@itemUpdate');
				Route::post('item-boosted', 'LazadaController@itemGetBoosted');
				Route::get('item-categories', 'LazadaController@itemGetCategories');
				Route::get('item-attributes', 'LazadaController@itemGetAttributes');
				Route::get('logistics', 'LazadaController@getLogistics');
			});
		});
	});

	Route::group(['prefix' => 'tokopedia' ,'middleware' => ['auth:api', 'throttle:rate_limit,1']], function() {
		Route::post('scanning-ecommerce', 'TokopediaController@getData');
		Route::post('tokopedia-save-item', 'TokopediaController@store');
	});
});

/*Route::middleware('auth:api')->get('/ecommerce', function (Request $request) {
    return $request->user();
});*/