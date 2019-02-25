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

// 注册路由
Route::get('/register', 'RegisterController@getRegister');
Route::post('/register/sendEmail', 'RegisterController@sendEmail');
Route::post('/register', 'RegisterController@postRegister');

// 登录路由
Route::get('/login','LoginController@userLogin');
Route::post('/login','LoginController@postUserLogin');
Route::get('/adminLogin','LoginController@adminLogin');
Route::post('/adminLogin','LoginController@postAdminLogin');

// 测试
Route::get('TestApi/test', 'Api\TestApiController@getTest');

// 验证码
Route::get('/getCaptcha', 'LoginController@getCaptcha');

//BaseController
Route::get('/','Admin\BaseController@getIndex');
Route::group(['prefix' => 'Base','namespace' => 'Admin', 'middleware' => 'admin'],function (){
    Route::get('menus','BaseController@getMenus');
    Route::get('secondMenus','BaseController@getSecondMenus');
    Route::get('secondMenusUrl','BaseController@getSecondMenusUrl');
    Route::get('childMenus','BaseController@getChildrenMenus');
});

//UserController
Route::group(['prefix' => 'User','namespace' => 'Admin', 'middleware' => 'admin'],function (){
    Route::get('list','UserController@getList');
    Route::get('form','UserController@getForm');
    Route::get('test','UserController@getTest');
    Route::post('status','UserController@postStatus');
});

//StoreController
Route::group(['prefix' => 'Store','namespace' => 'Admin', 'middleware' => 'admin'],function (){
    Route::get('list','StoreController@getList');
});

//MenusController
Route::group(['prefix' => 'Menus', 'namespace' => 'Admin', 'middleware' => 'admin'], function (){
    Route::get('list', 'MenusController@getList');
    Route::get('form', 'MenusController@getForm');
    Route::post('form', 'MenusController@postForm');
    Route::post('status', 'MenusController@postStatus');
    Route::post('delete', 'MenusController@postDelete');
});