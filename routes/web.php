<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebAppController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [WebAppController::class, 'login'])->name('login')->middleware('guest');
Route::post('/set-session', [WebAppController::class, 'setSession'])->name('setSession')->middleware('guest');
Route::get('/session', [WebAppController::class, 'session'])->name('session');
Route::group(['middleware' => 'auth'], function () {
    Route::get('/dashboard', [WebAppController::class, 'dashboard'])->name('dashboard');
    Route::get('/logout', [WebAppController::class, 'logout'])->name('logout');
    Route::get('/pesan', [WebAppController::class, 'pesan'])->name('pesan');
    Route::get('/pegawai', [WebAppController::class, 'pegawai'])->name('pegawai');
    Route::get('/kirim/{wa_pesan_id}', [WebAppController::class, 'kirim'])->name('kirim');
});
