<?php

Route::group(['prefix' => 'UserApi','namespace' => 'Api\User'],function (){
    Route::get('homePage','UserApiController@getHomePage');
});