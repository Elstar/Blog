<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\BlogController;
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
require __DIR__ . '/auth.php';

Route::controller(BlogController::class)->as('blog.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::middleware('auth')->group(function () {
        Route::get('{id}', 'show')->name('show');
        Route::controller(ArticleController::class)->as('article.')->prefix('{id}/article')->group(function () {
            Route::get('{article}', 'show')->name('show');
            Route::middleware('auth.admin')->group(function () {
                Route::get('{article}/get', 'get')->name('get');
                Route::post('/create', 'store')->name('create');
                Route::patch('{article}', 'update')->name('update');
                Route::delete('{article}', 'delete')->name('delete');
            });
        });
    });
});
