<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\TransferController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

Route::get('/login', [LoginController::class,'ShowLoginForm'])->name('login.form');
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


Route::get('/transfer', [TransferController::class,'showTransferPage'])->name('transfer');
Route::post('/sap/transfer', [TransferController::class, 'processTransfer'])->name('transfer.process');