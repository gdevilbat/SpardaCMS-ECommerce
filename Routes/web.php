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
					Route::get('marketplace', 'ShopeeController@marketplace')->middleware('core.menu')->name('marketplace-shopee');
					Route::group(['prefix' => 'api'], function() {
					    Route::get('master', 'ShopeeController@serviceMaster')->middleware('can:menu-ecommerce');

					    Route::group(['prefix' => 'schedule'], function() {
						    Route::get('item', 'ShopeeController@scheduleItem')->middleware('can:menu-ecommerce');
					    });

					    Route::group(['prefix' => 'discount'], function() {
						    Route::get('list', 'ShopeeController@getDiscountsList')->middleware('can:menu-ecommerce');
						    Route::get('detail', 'ShopeeController@getDiscountDetail')->middleware('can:menu-ecommerce');
						    Route::post('add-item', 'ShopeeController@addDiscountItem')->middleware('can:menu-ecommerce');
					    });
				    });
			    });

			    Route::group(['prefix' => 'lazada'], function() {
				    Route::get('authentication', 'LazadaController@authentication');
					Route::get('callback', 'LazadaController@callback');
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