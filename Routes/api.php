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
    Route::post('scrapping-product', 'ScrappingController@scrappingProduct');
	Route::post('scrapping-variant', 'ScrappingController@scrappingVariant');
	Route::post('scrapping-shopee', 'ScrappingController@scrappingShopee');
	Route::post('get-shopee-detail', 'ScrappingController@shopeeDetail');

	Route::group(['prefix' => 'shopee'], function() {
		Route::group(['namespace' => '\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Micro'], function() {
			Route::post('item-detail', 'ShopeeController@itemGetDetail');

			Route::group(['middleware' => ['auth:api','throttle:rate_limit,1']], function() {
				Route::post('shop-detail', 'ShopeeController@shopGetDetail');
				Route::post('item-list', 'ShopeeController@itemGetList');
				Route::post('item-update', 'ShopeeController@itemUpdate');
				Route::post('item-boosted', 'ShopeeController@itemGetBoosted');
			});
		});

		Route::get('item-publish-promotion', '\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\ShopeeController@publishItemPromotion');
		Route::get('shopee-categories', 'ShopeeController@getCategories');
	});

	Route::group(['prefix' => 'tokopedia' ,'middleware' => ['auth:api', 'throttle:rate_limit,1']], function() {
		Route::post('scanning-ecommerce', 'TokopediaController@getData');
	});
});

/*Route::middleware('auth:api')->get('/ecommerce', function (Request $request) {
    return $request->user();
});*/