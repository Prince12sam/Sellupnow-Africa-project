<?php

use Illuminate\Http\Request;


Route::middleware('auth:api')->get('/pluginmanage', function (Request $request) {
    return $request->user();
});
