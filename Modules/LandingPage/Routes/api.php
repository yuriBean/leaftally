<?php

use Illuminate\Http\Request;

Route::middleware('auth:api')->get('/landingpage', function (Request $request) {
    return $request->user();
});