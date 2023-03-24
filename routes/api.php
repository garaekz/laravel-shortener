<?php

use App\Http\Controllers\API\V1\UrlController;
use App\Http\Controllers\Auth\SocialAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'auth'], function () {
    Route::get('{provider}/redirect', [SocialAuthController::class, 'redirect']);
    Route::get('{provider}/callback', [SocialAuthController::class, 'callback']);
    Route::post('logout', [SocialAuthController::class, 'logout'])->middleware('auth:sanctum');
});

Route::group([
    'prefix' => 'v1',
    'middleware' => [
        'auth:sanctum',
        'throttle:60,1',
        'ability:api:read,api:write,member',
    ],
], function () {
    Route::apiResource('urls', UrlController::class);
});
