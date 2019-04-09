<?php

Route::group(['prefix' => 'UserApi','namespace' => 'Api\User','middleware' => 'user_address'],function (){
    Route::get('homePage','UserApiController@getHomePage');
    Route::get('hot','UserApiController@getHotStore');
    Route::get('recommended','UserApiController@getRecommendedStore');
    Route::post('logout','UserApiController@logout');
    Route::get('store', 'UserApiController@getStore');
    Route::get('storeFood', 'UserApiController@getStoreFood');
    Route::get('myAddress', 'UserApiController@getMyAddress');
    Route::get('pushMessage', 'UserApiController@pushMessage');
    Route::get('deleteMessage', 'UserApiController@deleteMessage');
});

Route::get('UserApi/addressForm', 'Api\User\UserApiController@getAddressForm');
Route::post('UserApi/addressForm', 'Api\User\UserApiController@postAddressForm');
Route::get('UserApi/deleteAddress', 'Api\User\UserApiController@deleteAddress');

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

Route::group(['prefix' => 'DriverApi','namespace' => 'Api\Driver'],function (){
    Route::get('index','DriverApiController@getIndex');
    Route::get('acceptOrder','DriverApiController@acceptOrder');
    Route::get('refuseOrder','DriverApiController@refuseOrder');
    Route::get('finishOrder','DriverApiController@finishOrder');
});