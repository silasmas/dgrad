<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContreventionController;
use App\Http\Controllers\TransactionController;

Route::get('/', action: [ContreventionController::class, 'index'])->name('home');
Route::get('ref/{ref}', action: [ContreventionController::class, 'index'])->name(name: 'ref');
Route::get('/infraction', action: [ContreventionController::class, 'infraction'])->name(name: 'infraction');

Route::post('searchMatricule', action: [ContreventionController::class, 'searchMatricule'])->name('searchMatricule');
Route::post('paieInfraction', action: [ContreventionController::class, 'paieInfraction'])->name('paieInfraction');
Route::post('authAgent', action: [ContreventionController::class, 'create'])->name(name: 'authAgent');
Route::post('registerInfra', action: [ContreventionController::class, 'store'])->name(name: 'registerInfra');
Route::post('storeTransaction', action: [TransactionController::class, 'store'])->name(name: 'storeTransaction');

Route::post('sms', action: [TransactionController::class, 'sms'])->name(name: 'sms');
Route::get('/checkTransactionStatus', [TransactionController::class, 'checkTransactionStatus'])->name('checkTransactionStatus');


Route::get('/paid/{amount}/{currency}/{code}', [ContreventionController::class, 'paid'])->whereNumber(['amount', 'code'])->name('paid');

Route::get('findByPhone', action: [TransactionController::class, 'findByPhone'])->name(name: 'findByPhone');
Route::get('findByOrder', action: [TransactionController::class, 'findByOrderNumber'])->name(name: 'findByOrder');
Route::get('findInfra', action: [ContreventionController::class, 'showInfra'])->name(name: 'findInfra');
Route::get('/', action: [ContreventionController::class, 'index'])->name('home');
