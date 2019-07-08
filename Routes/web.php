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

Route::group(['prefix' => 'control', 'middleware' => 'core.menu'], function() {
    
	Route::group(['middleware' => 'core.auth'], function() {

		Route::group(['prefix' => 'product'], function() {
	        /*=============================================
	        =            Product CMS            =
	        =============================================*/
	        
			    Route::get('master', 'ProductController@index')->middleware('can:menu-ecommerce')->name('product');
			    Route::get('form', 'ProductController@create')->name('product');
			    Route::post('form', 'ProductController@store')->middleware('can:create-ecommerce')->name('product');
			    Route::put('form', 'ProductController@store')->name('product');
			    Route::delete('form', 'ProductController@destroy')->name('product');

			    Route::group(['prefix' => 'api'], function() {
				    Route::get('master', 'ProductController@serviceMaster')->middleware('can:menu-ecommerce');
			    });
	        
	        /*=====  End of Product CMS  ======*/
		});

		Route::group(['prefix' => 'product-category'], function() {
	        /*=============================================
	        =            Category CMS            =
	        =============================================*/
	        
			    Route::get('master', 'CategoryController@index')->middleware('can:menu-ecommerce')->name('product-category');
			    Route::get('form', 'CategoryController@create')->name('product-category');
			    Route::post('form', 'CategoryController@store')->middleware('can:create-ecommerce')->name('product-category');
			    Route::put('form', 'CategoryController@store')->name('product-category');
			    Route::delete('form', 'CategoryController@destroy')->name('product-category');

			    Route::group(['prefix' => 'api'], function() {
				    Route::get('master', 'CategoryController@serviceMaster')->middleware('can:menu-ecommerce');
			    });
	        
	        /*=====  End of Category CMS  ======*/
		});

		Route::group(['prefix' => 'product-tag'], function() {
	        /*=============================================
	        =            Tag CMS            =
	        =============================================*/
	        
			    Route::get('master', 'TagController@index')->middleware('can:menu-ecommerce')->name('product-tag');
			    Route::get('form', 'TagController@create')->name('product-tag');
			    Route::post('form', 'TagController@store')->middleware('can:create-ecommerce')->name('product-tag');
			    Route::put('form', 'TagController@store')->name('product-tag');
			    Route::delete('form', 'TagController@destroy')->name('product-tag');

			    Route::group(['prefix' => 'api'], function() {
				    Route::get('master', 'TagController@serviceMaster')->middleware('can:menu-ecommerce');
			    });
	        
	        /*=====  End of Tag CMS  ======*/
		});
        
	});
});