<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\BoardController;
use App\Http\Controllers\API\TaskController;

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

Route::middleware('auth:passport')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [RegisterController::class, 'login'])->name('login');

Route::middleware('auth:api')->group( function () {
    Route::resource('boards', BoardController::class);
    Route::resource('products', ProductController::class);
    Route::get('tasks', [TaskController::class, 'index']);
    Route::post('tasks', [TaskController::class, 'store']);
    Route::get('task/{id}', [TaskController::class, 'show']);
    Route::put('task/{id}', [TaskController::class, 'update']);
    Route::delete('task/{id}', [TaskController::class, 'destroy']);

    Route::get('user/{id}', [RegisterController::class, 'userDetails']);
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
    //return $request->user()->board;
});
