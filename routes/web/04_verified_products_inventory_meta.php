<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    ProductServiceController, ProductStockController, ProductServiceCategoryController,
    TaxController, ProductServiceUnitController
};

Route::group(['middleware' => ['auth','2fa']], function () {

    // Product & Inventory
    Route::get('productservice/index', [ProductServiceController::class, 'index'])->name('productservice.index')->middleware(['auth','XSS']);
    Route::get('export/productservice', [ProductServiceController::class, 'export'])->name('productservice.export');

    Route::get('import/productservice/file', [ProductServiceController::class, 'importFile'])->name('productservice.file.import');
    Route::delete('/productservice/bulk-destroy', [ProductServiceController::class, 'bulkDestroy'])
    ->name('productservice.bulk-destroy');
    Route::post('productservice/{id}/restore', [ProductServiceController::class, 'restore'])->name('productservice.restore');
Route::delete('productservice/{id}/force', [ProductServiceController::class, 'forceDestroy'])->name('productservice.force');

Route::get('/productservice/short', [ProductServiceController::class, 'createShort'])->name('productservice.create-short');
    Route::resource('productservice', ProductServiceController::class)->except('index')->middleware(['auth','XSS','revalidate']);
    Route::post('/productservice/export-selected', [ProductServiceController::class, 'exportSelected'])
    ->name('productservice.export-selected')
    ->middleware(['auth','XSS','revalidate']);
    Route::post('product_short', [ProductServiceController::class, 'product_short'])->name('product_short');

    Route::resource('productstock', ProductStockController::class)->middleware(['auth','XSS','revalidate']);
    Route::post('/productstock/export-selected', [ProductStockController::class, 'exportSelected'])
    ->name('productstock.export-selected');

    // Product meta
    Route::post('product-category/bulk-destroy', [ProductServiceCategoryController::class, 'bulkDestroy'])
        ->name('product-category.bulk-destroy');

    Route::get('product-category/export', [ProductServiceCategoryController::class, 'export'])
        ->name('product-category.export');
    Route::post('product-category-short', [ProductServiceCategoryController::class, 'short'])->name('product-category-short');

    Route::post('product-category/export-selected', [ProductServiceCategoryController::class, 'exportSelected'])
        ->name('product-category.export-selected');
    Route::resource('product-category', ProductServiceCategoryController::class)->middleware(['auth','XSS','revalidate']);
    Route::post('product-category/getaccount', [ProductServiceCategoryController::class, 'getAccount'])->name('productServiceCategory.getaccount')->middleware(['auth','XSS','revalidate']);

    Route::resource('taxes', TaxController::class)->middleware(['auth','XSS','revalidate','feature:tax_management_enabled']);
    Route::post('product-unit-short', [ProductServiceUnitController::class, 'short'])->name('product-unit-short');
    Route::post('product-unit/bulk-destroy', [ProductServiceUnitController::class, 'bulkDestroy'])
        ->name('product-unit.bulk-destroy');

    // Exports
    Route::get('product-unit/export', [ProductServiceUnitController::class, 'export'])
        ->name('product-unit.export');

    Route::post('product-unit/export-selected', [ProductServiceUnitController::class, 'exportSelected'])
        ->name('product-unit.export-selected');
    Route::resource('product-unit', ProductServiceUnitController::class)->middleware(['auth','XSS','revalidate']);
});
