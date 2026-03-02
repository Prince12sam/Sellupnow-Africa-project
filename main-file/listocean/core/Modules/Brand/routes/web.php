<?php

use Illuminate\Support\Facades\Route;
use Modules\Brand\app\Http\Controllers\BrandController;

Route::group(['prefix' => 'admin/brand', 'middleware' => ['auth:admin', 'setlang']], function () {
    Route::match(['get', 'post'], '/all-brand', [BrandController::class, 'all_brand'])->name('admin.brand.all')->permission('brand-list');
    Route::post('edit-brand/{id?}', [BrandController::class,'edit_brand'])->name('admin.brand.edit')->permission('brand-edit');
    Route::post('change-status/{id}', [BrandController::class,'change_status_brand'])->name('admin.brand.status')->permission('brand-status-change');
    Route::post('delete/{id}', [BrandController::class,'delete_brand'])->name('admin.brand.delete')->permission('brand-delete');
    Route::post('bulk-action', [BrandController::class,'bulk_action_brand'])->name('admin.brand.delete.bulk.action')->permission('brand-bulk-delete');
    Route::get('paginate/data', [BrandController::class,'pagination'])->name('admin.brand.paginate.data');
    Route::get('search-brand', [BrandController::class,'search_brand'])->name('admin.brand.search');
});
