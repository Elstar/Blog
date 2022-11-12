<?php

use App\Http\Controllers\API\ArticleController;
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

Route::middleware(['auth.basic', 'auth.admin'])->group(function () {
    Route::group(['prefix' => 'article', 'as' => '.article', 'controller' => ArticleController::class], function () {
        Route::post('store', 'store')->name('store');
        Route::put('{id}', 'update')->name('update');
        Route::delete('{id}', 'destroy')->name('destroy');
    });
});
