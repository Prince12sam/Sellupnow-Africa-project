<?php

use Illuminate\Support\Facades\Route;

use \Modules\CountryManage\app\Http\Controllers\CountryController;
use \Modules\CountryManage\app\Http\Controllers\StateController;
use \Modules\CountryManage\app\Http\Controllers\CityController;

Route::group(['prefix' => 'admin/location', 'middleware' => ['auth:admin', 'setlang']], function () {
    Route::group(['prefix' => 'country'], function () {
        Route::match(['get', 'post'], '/all-country', [CountryController::class, 'all_country'])->name('admin.country.all')->permission('country-list');
        Route::post('edit-country/{id?}', [CountryController::class,'edit_country'])->name('admin.country.edit')->permission('country-edit');
        Route::post('change-status/{id}', [CountryController::class,'change_status_country'])->name('admin.country.status')->permission('country-status-change');
        Route::post('delete/{id}', [CountryController::class,'delete_country'])->name('admin.country.delete')->permission('country-delete');
        Route::post('bulk-action', [CountryController::class,'bulk_action_country'])->name('admin.country.delete.bulk.action')->permission('country-bulk-delete');
        Route::get('paginate/data', [CountryController::class,'pagination'])->name('admin.country.paginate.data');
        Route::get('search-country', [CountryController::class,'search_country'])->name('admin.country.search');
        Route::get('csv/import', [CountryController::class,'import_settings'])->name('admin.country.import.csv.settings')->permission('country-csv-file-import');
        Route::post('csv/import', [CountryController::class,'update_import_settings'])->name('admin.country.import.csv.update.settings');
        Route::post('csv/import/database', [CountryController::class,'import_to_database_settings'])->name('admin.country.import.database');
    });

    Route::group(['prefix' => 'state'], function () {
        Route::controller(StateController::class)->group(function () {
            Route::match(['get', 'post'], 'all-state', 'all_state')->name('admin.state.all')->permission('state-list');
            Route::post('edit-state/{id?}', 'edit_state')->name('admin.state.edit')->permission('state-edit');
            Route::post('change-status/{id}', 'change_status_state')->name('admin.state.status')->permission('state-status-change');
            Route::post('delete/{id}', 'delete_state')->name('admin.state.delete')->permission('state-delete');
            Route::post('bulk-action', 'bulk_action_state')->name('admin.state.delete.bulk.action')->permission('state-bulk-delete');
            Route::get('paginate/data', 'pagination')->name('admin.state.paginate.data');
            Route::get('search-state', 'search_state')->name('admin.state.search');
            Route::get('csv/import', 'import_settings')->name('admin.state.import.csv.settings')->permission('state-csv-file-import');
            Route::post('csv/import', 'update_import_settings')->name('admin.state.import.csv.update.settings');
            Route::post('csv/import/database', 'import_to_database_settings')->name('admin.state.import.database');
        });
    });

    Route::group(['prefix' => 'city'], function () {
        Route::controller(CityController::class)->group(function () {
            Route::match(['get', 'post'], 'all-city', 'all_city')->name('admin.city.all')->permission('city-list');
            Route::post('edit-city/{id?}', 'edit_city')->name('admin.city.edit')->permission('city-edit');
            Route::post('change-status/{id}', 'city_status')->name('admin.city.status')->permission('city-status-change');
            Route::post('delete/{id}', 'delete_city')->name('admin.city.delete')->permission('city-delete');
            Route::post('bulk-action', 'bulk_action_city')->name('admin.city.delete.bulk.action')->permission('city-bulk-delete');
            Route::get('paginate/data', 'pagination')->name('admin.city.paginate.data');
            Route::get('search-city', 'search_city')->name('admin.city.search');
            Route::get('csv/import', 'import_settings')->name('admin.city.import.csv.settings')->permission('city-csv-file-import');
            Route::post('csv/import', 'update_import_settings')->name('admin.city.import.csv.update.settings');
            Route::post('csv/import/database', 'import_to_database_settings')->name('admin.city.import.database');
        });
    });
});
