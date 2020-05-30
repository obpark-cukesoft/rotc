<?php

use App\User;
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

Route::get('/', function () {
    return 'rotc';
});

//Auth::routes();
Auth::routes(['register' => false, 'confirm' => false, 'email' => false, 'reset' => false]);

//Route::get('/home', 'HomeController@index')->name('home');

// route for admin
Route::group(['middleware' => 'auth.level:'.USER::LEVEL_ADMIN], function () {
    Route::get('/', 'HomeController@index')->name('home');
    Route::get('/home', 'HomeController@index')->name('home');

    //http://localhost/rebuild-code
    Route::get('/rebuild-code', 'CodeController@rebuild')->name('home');


    /**
     * 게시판
     */
    Route::get('/board/{boardId}', 'PostController@index')->where(['boardId' => '[0-9]+']);
    Route::get('/board/{boardId}/{page}/{rowsPerPage}/{keyword?}', 'PostController@index')->where(['boardId' => '[0-9]+']);
    Route::post('/post/{boardId}', 'PostController@store')->where(['boardId' => '[0-9]+']);
    Route::get('/post/{id}', 'PostController@show')->where(['id' => '[0-9]+']);
    Route::put('/post/{id}', 'PostController@update')->where(['id' => '[0-9]+']);
    Route::delete('/post/{id}', 'PostController@destroy')->where(['id' => '[0-9]+']);
    /*
    Route::get('/posts/{bbsId}/create', 'PostController@create')->where(['bbsId' => '[0-9]+']);
    Route::resource('/post', 'PostController');
    Route::post('/post/comment', 'PostController@storeComment');
    Route::delete('/post/comment/{commentId}', 'PostController@destroyComment')->where(['commentId' => '[0-9]+']);
    Route::delete('/post/file/{fileId}', 'PostController@destroyFile')->where(['fileId' => '[0-9]+']); */


    /**
     * 회원관리
     */
    Route::get('/member', 'MemberController@index');
    Route::get('/member/{page}/{rowsPerPage}/{keyword?}', 'MemberController@index');
    Route::get('/member/{id}', 'MemberController@show')->where(['id' => '[0-9]+']);
    Route::post('/member', 'MemberController@store');
    Route::put('/member/{id}', 'MemberController@update')->where(['id' => '[0-9]+']);
    Route::put('/member/applyStatus', 'MemberController@applyStatus');
    Route::delete('/member/{id}', 'MemberController@destroy')->where(['id' => '[0-9]+']);
    Route::post('/member/file/{type}', 'MemberController@storeFile');
    Route::delete('/member/file/{type}/{id}', 'MemberController@destroyFile')->where(['id' => '[0-9]+']);
});
