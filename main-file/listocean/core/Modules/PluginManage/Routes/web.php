<?php


/* ------------------------------------------
     Plugin Manage ADMIN ROUTES
-------------------------------------------- */

use Illuminate\Support\Facades\Route;
use Modules\PluginManage\Http\Controllers\PluginManageController;


Route::group(['prefix' => 'admin', 'middleware' => ['auth:admin', 'setlang']], function () {
    Route::get("plugin-manage/all",[PluginManageController::class,"index"])->name("admin.plugin.manage.all")->permission('plugins-list');
    Route::get("plugin-manage/new",[PluginManageController::class,"add_new"])->name("admin.plugin.manage.new")->permission('plugins-add');
    Route::post("plugin-manage/new",[PluginManageController::class,"store_plugin"]);
    Route::post("plugin-manage/delete",[PluginManageController::class,"delete_plugin"])->name("admin.plugin.manage.delete")->permission('plugins-delete');
    Route::post("plugin-manage/status",[PluginManageController::class,"change_status"])->name("admin.plugin.manage.status.change")->permission('plugins-status-change');
});
