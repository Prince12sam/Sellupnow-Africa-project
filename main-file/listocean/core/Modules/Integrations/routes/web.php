<?php

use Illuminate\Support\Facades\Route;
use Modules\Integrations\app\Http\Controllers\IntegrationsController;


Route::group(['prefix' => 'admin', 'middleware' => ['auth:admin', 'setlang']], function () {
    Route::match(['get','post'],"integrations-manage",[IntegrationsController::class,"store"])->name('admin.integration')->permission('integration-list');
    Route::post("integrations-manage/active",[IntegrationsController::class,"activate"])->name('admin.integration.activation');
});
