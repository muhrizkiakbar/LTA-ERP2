<?php

use App\Http\Controllers\Api\V1\ApiController;
use Illuminate\Http\Request;
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
Route::post('autoSyncPng', [ApiController::class, 'autoSyncPng']);
Route::post('autoSyncMix', [ApiController::class, 'autoSyncMix']);
// Route::post('generateInvoiceFP', [ApiController::class, 'generateInvoiceFP']);
// Route::post('updateInvoiceFP', [ApiController::class, 'updateInvoiceFP']);
// Route::post('discountItem', [ApiController::class, 'discountItem']);
// Route::post('stokItem', [ApiController::class, 'stokItem']);
// Route::post('stokItemTaa', [ApiController::class, 'stokItemTaa']);

// PA FILE
// Route::post('generatePAFile', [ApiController::class, 'generatePAFile']);
// Route::post('/next/pa-file', [ApiController::class, 'generatePaNext']);

// Route::post('getVoucherDelivery',[ApiController::class ,'getVoucherDelivery']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
