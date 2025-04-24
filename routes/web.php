<?php

use App\Http\Controllers\Controller;
use App\Http\Controllers\RombonganController;
use App\Http\Controllers\UserController;
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
Route::get('/cal', [RombonganController::class, 'cal'])->middleware('auth');
Route::post('/tambah-invoice', [RombonganController::class, 'tambahInvoice'])->name('tambah.invoice');

//BO
Route::get('/backoffice', [RombonganController::class, 'bo'])->middleware('auth')->name('bo');
Route::get('/detail_transaksi', [RombonganController::class, 'detail_transaksi'])->middleware('auth');
Route::get('/update_status_transaksi', [RombonganController::class, 'update_status_transaksi'])->middleware('auth');

//akun
Route::get('/akun', [UserController::class, 'akun'])->middleware('auth'); 
Route::post('/add.user', [UserController::class, 'store']);
Route::get('/del.user', [UserController::class, 'destroy']);


//Fatch
Route::get('/frontoffice/rombongan-data', [RombonganController::class, 'getRombonganData']);
Route::get('/frontoffice/rombongan-data-transaksi', [RombonganController::class, 'getRombonganDatatransaksi']);
Route::get('/rombongan-detail', [RombonganController::class, 'getRombonganDetail']);
Route::get('/rombongans-data', [RombonganController::class, 'getRombongansData']);
Route::get('/invoice-detail', [RombonganController::class, 'invoiceDetail']);

 
 
//Reset db

Route::get('/resetuserdb', [UserController::class, 'reset']);
Route::get('/reset.login.user', [UserController::class, 'reset_login']);