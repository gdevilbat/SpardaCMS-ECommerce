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
		Route::get('authentication', 'ShopeeController@authentication');
		Route::get('callback', 'ShopeeController@callback');
		Route::post('item-detail', 'ShopeeController@getItemDetail');
	});
});

/*Route::middleware('auth:api')->get('/ecommerce', function (Request $request) {
    return $request->user();
});*/