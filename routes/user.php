<?php

Route::group(['prefix' => 'UserApi','namespace' => 'Api\User','middleware' => 'user_address'],function (){
    Route::get('homePage','UserApiController@getHomePage');
    Route::get('hot','UserApiController@getHotStore');
    Route::get('recommended','UserApiController@getRecommendedStore');
    Route::post('logout','UserApiController@logout');
    Route::get('store', 'UserApiController@getStore');
});

Route::get('UserApi/addressForm', 'Api\User\UserApiController@getAddressForm');
Route::post('UserApi/addressForm', 'Api\User\UserApiController@postAddressForm');