<?php

use Illuminate\Support\Facades\Route;

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
Route::match(['get', 'post'], 'login', 'Admin\LoginController@login');

Route::group(['middleware' => ['verifyLogin']], function () {
    Route::get('/', 'Admin\ChatController@chat');
    Route::get('/chat/getUserRelation', 'Admin\ChatController@getUserRelation');
    Route::get('/chat/getGroupUser', 'Admin\ChatController@getGroupUser');
    Route::get('/chat/searchUser', 'Admin\ChatController@searchUser');
    Route::post('/chat/addFriend', 'Admin\ChatController@addFriend');
    Route::get('/chat/noticeList', 'Admin\ChatController@noticeList');
    Route::post('/chat/agreeFriend', 'Admin\ChatController@agreeFriend');
    Route::post('/chat/updateStatus', 'Admin\ChatController@updateStatus');
    Route::post('/chat/updateSign', 'Admin\ChatController@updateSign');
    Route::post('/chat/refuseFriend', 'Admin\ChatController@refuseFriend');
});

