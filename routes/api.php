<?php

use Illuminate\Http\Request;
use App\Http\Controllers\TransferController;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/transfer', [TransferController::class, 'processTransfer'])->middleware('auth');
