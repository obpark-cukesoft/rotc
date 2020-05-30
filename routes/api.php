<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::get('/', function () {
    return 'welcome';
});

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/



//공지사항 api
use App\Http\Resources\PostCollection;
use App\Post;
Route::get('/notice', function () {
    return new PostCollection(Post::where('board_id', 1)
        ->orderBy('id', 'desc')
        ->paginate(5));
});

Route::get('/notice/{id}', function ($id) {
    return Post::findOrFail($id);
});




Route::middleware('auth:api')->group(function () {
    Route::get('/member', 'MemberController@show');
    //Route::put('/member', 'MemberController@test');
    Route::put('/member', 'MemberController@update');
});
