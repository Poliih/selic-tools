<?php

use App\Http\Controllers\SelicController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/selic', [SelicController::class, 'historico']);
Route::get('/simulacao', [SelicController::class, 'simular']);
