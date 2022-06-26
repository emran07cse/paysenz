<?php

use Illuminate\Http\Request;

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

Route::post('/v1.0/pay', 'RequestController@index')->name('pay')->middleware('auth:api');
Route::post('/v1.0/status', 'RequestController@status')->name('status')->middleware('auth:api');
Route::post('/v1.0/verify', 'RequestController@verify')->name('verify')->middleware('auth:api');
Route::post('/v1.0/client-info', 'RequestController@getClientInfo')->name('client.info')->middleware('auth:api');
Route::post('/v1.0/retry', 'ProcessRequestController@retry')->name('pay.retry')->middleware('auth:api');
Route::post('/bkash/checkout/create/payment', 'BkashCheckoutController@createPaymentRequest');
Route::post('/bkash/checkout/execute/payment', 'BkashCheckoutController@executePaymentRequest');
