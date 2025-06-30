<?php

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

Route::get('/catalogue/{business_id}/{location_id}', [\Modules\ProductCatalogue\Http\Controllers\ProductCatalogueController::class, 'index']);
Route::get('/show-catalogue/{business_id}/{product_id}', [\Modules\ProductCatalogue\Http\Controllers\ProductCatalogueController::class, 'show']);
Route::post('/add-to-cart', [\Modules\ProductCatalogue\Http\Controllers\ProductCatalogueController::class, 'addToCart']);
Route::post('/update-cart', [\Modules\ProductCatalogue\Http\Controllers\ProductCatalogueController::class, 'updateCart']);
Route::post('delete-cart-item', [\Modules\ProductCatalogue\Http\Controllers\ProductCatalogueController::class, 'deleteCartItem']);
Route::post('/save-contact', [\Modules\ProductCatalogue\Http\Controllers\ProductCatalogueController::class, 'contactSave']);
Route::post('/save-sale', [\Modules\ProductCatalogue\Http\Controllers\ProductCatalogueController::class, 'submit'])->name('submit-order');
Route::get('/view-cart/{business_id}/{location_id}', [\Modules\ProductCatalogue\Http\Controllers\ProductCatalogueController::class, 'viewCart'])->name('view-cart');

Route::post('/update-account', [\Modules\ProductCatalogue\Http\Controllers\ProductCatalogueController::class, 'updateAccount'])->name('update-account');

Route::middleware('web', 'authh', 'auth', 'SetSessionData', 'language', 'timezone', 'AdminSidebarMenu')->prefix('product-catalogue')->group(function () {
    Route::get('catalogue-qr', [\Modules\ProductCatalogue\Http\Controllers\ProductCatalogueController::class, 'generateQr']);

    Route::get('install', [\Modules\ProductCatalogue\Http\Controllers\InstallController::class, 'index']);
    Route::post('install', [\Modules\ProductCatalogue\Http\Controllers\InstallController::class, 'install']);
    Route::get('install/uninstall', [\Modules\ProductCatalogue\Http\Controllers\InstallController::class, 'uninstall']);
    Route::get('install/update', [\Modules\ProductCatalogue\Http\Controllers\InstallController::class, 'update']);
});
