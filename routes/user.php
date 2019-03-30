<?php

Route::group(['prefix' => 'UserApi','namespace' => 'Api\User','middleware' => 'user_address'],function (){
    Route::get('homePage','UserApiController@getHomePage');
    Route::get('hot','UserApiController@getHotStore');
    Route::get('recommended','UserApiController@getRecommendedStore');
    Route::post('logout','UserApiController@logout');
    Route::get('store', 'UserApiController@getStore');
    Route::get('storeFood', 'UserApiController@getStoreFood');
});

Route::get('UserApi/addressForm', 'Api\User\UserApiController@getAddressForm');
Route::post('UserApi/addressForm', 'Api\User\UserApiController@postAddressForm');

Route::group(['prefix' => 'OrderApi','namespace' => 'Api\Order','middleware' => 'user_address'],function (){
    Route::post('beforeOrdering', 'OrderApiController@beforeOrdering');
    Route::post('ordering', 'OrderApiController@ordering');
    Route::get('selectAddress', 'OrderApiController@selectAddress');
    Route::get('chooseAddress', 'OrderApiController@chooseAddress');
    Route::get('orderDetail', 'OrderApiController@orderDetail');
    Route::get('orderList', 'OrderApiController@orderList');
    Route::get('orderListAjax', 'OrderApiController@orderListAjax');
    Route::get('cancelOrder', 'OrderApiController@cancelOrder');
});