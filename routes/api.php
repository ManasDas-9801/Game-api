<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserAuthController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

 
Route::post('register', [UserAuthController::class, 'register']);
Route::post('login', [UserAuthController::class, 'login']);
Route::get('/get-top-player', [UserAuthController::class, 'getTopPlayer']);

Route::group(['middleware' => 'jwt.verify'], function () {
    Route::post('logout', [UserAuthController::class, 'logout']);
    Route::get('/user-profile', [UserAuthController::class, 'userProfile']);
    Route::get('/save-score', [UserAuthController::class, 'saveGameScore']);
});