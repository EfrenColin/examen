<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\Auth\AuthController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});




Route::post('register',[AuthController::class,'register'])->name('register');
Route::post('login',[AuthController::class,'login'])->name('login.attemp');


Route::get('/', function () {
    return response()->json([
        'message'  => "Favor de iniciar sesión.",
        ], 401);
})->name('login');


Route::middleware(['auth:api'])->group(function () {
    Route::post('logout/{token}',[AuthController::class,'logout'])->name('logout');
    Route::apiResource('posts', PostController::class);
});