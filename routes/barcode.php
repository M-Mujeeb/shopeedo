<?php

use App\Http\Controllers\BarcodeController;

Route::group(['prefix' => 'admin/barcode', 'middleware' => ['auth', 'admin']], function () {
    Route::controller(BarcodeController::class)->group(function () {
        Route::get('/', 'index')->name('barcode.index');
    });
});

Route::group(['prefix' => 'seller/barcode', 'middleware' => ['auth', 'seller']], function () {
    Route::controller(BarcodeController::class)->group(function () {
        Route::get('/', 'sellerBarcode')->name('barcode.seller');
    });
});
