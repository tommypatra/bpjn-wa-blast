<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\UtilityController;
use App\Http\Controllers\WaPesanController;
use App\Http\Controllers\KirimPesanController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', [AuthController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/jumlah_pesan_bulanan/{thn}', [UtilityController::class, 'jumlahPesanBulanan']);
    Route::get('/jumlah_pegawai', [UtilityController::class, 'jumlahPegawai']);

    Route::resource('pesan', WaPesanController::class);
    Route::resource('pegawai', PegawaiController::class);
    Route::resource('kirim', KirimPesanController::class);
    Route::delete('/tujuan/{id}', [KirimPesanController::class, 'destroyTujuan']);
});
