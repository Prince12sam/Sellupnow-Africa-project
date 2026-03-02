<?php

use Illuminate\Support\Facades\Route;
use Modules\RolePermission\app\Http\Controllers\RolePermissionController;
use Modules\RolePermission\app\Http\Controllers\AdminManageController;



Route::group([], function () {
    Route::resource('rolepermission', RolePermissionController::class)->names('rolepermission');
});


//admin manage
Route::group(['prefix'=>'admin/manage','middleware' => ['auth:admin','setlang']],function(){
    Route::controller(AdminManageController::class)->group(function () {
        Route::get('all-admins','all_admins')->name('admin.all');
        Route::match(['get','post'],'create/new-admin', 'create_admin')->name('admin.create');
        Route::match(['get','post'],'edit/admin/{id}', 'edit_admin')->name('admin.edit');
        Route::post('delete/admin/{id}', 'delete_admin')->name('admin.delete');
        Route::post('change/admin/password', 'change_password')->name('admin.password.change');
    });

    Route::controller(RolePermissionController::class)->group(function () {
        Route::match(['get','post'],'permission/role/all', 'all_role')->name('admin.role.create');
        Route::post('permission/role/edit', 'edit_role')->name('admin.role.edit');
        Route::get('permission/role/assign/{id}', 'permission')->name('admin.role.permission');
        Route::post('permission/role/create/{id}', 'create_permission')->name('admin.role.permission.create');
        Route::post('permission/role/delete/{id}', 'delete_role')->name('admin.role.delete');
    });
});
