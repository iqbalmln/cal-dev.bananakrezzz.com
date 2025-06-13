<?php

use App\Http\Controllers\Controller;
use App\Http\Controllers\RombonganController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CabangController;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;



//LOGIN

Route::get('/', [UserController::class, 'index'])->name('login');
Route::post('/login', [UserController::class, 'login']);
Route::get('/logout', [UserController::class, 'logout']); 

//FO
Route::get('/frontoffice', [RombonganController::class, 'fo'])->middleware('auth')->name('fo');
Route::post('/add.rombongan', [RombonganController::class, 'store']);
Route::get('/update.database', [RombonganController::class, 'deleteAll'])->middleware('auth');


//KASIR
Route::get('/kasir', [RombonganController::class, 'kasir'])->middleware('auth')->name('kasir');
Route::get('/master', [UserController::class, 'master'])->middleware('auth')->name('master');
Route::get('/cal', [RombonganController::class, 'cal'])->middleware('auth');
Route::post('/tambah-invoice', [RombonganController::class, 'tambahInvoice'])->name('tambah.invoice');

//BO
Route::get('/backoffice', [RombonganController::class, 'bo'])->middleware('auth')->name('bo');
Route::get('/detail_transaksi', [RombonganController::class, 'detail_transaksi'])->middleware('auth');
Route::get('/update_status_transaksi', [RombonganController::class, 'update_status_transaksi'])->middleware('auth');
Route::get('/export-excel', [RombonganController::class, 'export_excel'])->middleware('auth')->name('export_excel');
Route::get('/sync-api', [RombonganController::class, 'sync_api'])->middleware('auth')->name('sync_api');
Route::get('/sync-api-success', [RombonganController::class, 'sync_api_success'])->middleware('auth')->name('sync_api');

//akun
Route::get('/akun', [UserController::class, 'akun'])->middleware('auth'); 
Route::get('/akun-master', [UserController::class, 'akun_master'])->middleware('auth'); 
Route::post('/add.user', [UserController::class, 'store']);
Route::get('/del.user', [UserController::class, 'destroy']);
Route::get('/cabang', [CabangController::class, 'cabang'])->middleware('auth'); 
Route::post('/add.cabang', [CabangController::class, 'store']);
Route::get('/del.cabang', [CabangController::class, 'destroy']);


//Fatch
Route::get('/frontoffice/rombongan-data', [RombonganController::class, 'getRombonganData']);
Route::get('/frontoffice/rombongan-data-transaksi', [RombonganController::class, 'getRombonganDatatransaksi']);
Route::get('/rombongan-detail', [RombonganController::class, 'getRombonganDetail']);
Route::get('/rombongans-data', [RombonganController::class, 'getRombongansData']);
Route::get('/invoice-detail', [RombonganController::class, 'invoiceDetail']);

// API
Route::get('/rombongan/{id}', [ApiController::class, 'rombongan']);
 
//Reset db

Route::get('/resetuserdb', [UserController::class, 'reset']);
Route::get('/reset.login.user', [UserController::class, 'reset_login']);