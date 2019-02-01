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

//BaseController
Route::get('/','Admin\BaseController@getIndex');
Route::group(['prefix' => 'Base','namespace' => 'Admin'],function (){
    Route::get('menus','BaseController@getMenus');
    Route::get('childMenus','BaseController@getChildrenMenus');
});

//UserController
Route::group(['prefix' => 'User','namespace' => 'Admin'],function (){
    Route::get('list','UserController@getList');
    Route::post('status','UserController@postStatus');
});

//StoreController
Route::group(['prefix' => 'Store','namespace' => 'Admin'],function (){
    Route::get('list','StoreController@getList');
});

//MenusController
Route::group(['prefix' => 'Menus', 'namespace' => 'Admin'], function (){
    Route::get('list', 'MenusController@getList');
    Route::get('form', 'MenusController@getForm');
    Route::post('form', 'MenusController@postForm');
});