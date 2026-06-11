<?php

use App\Http\Controllers\LocaleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/locale/{locale}', LocaleController::class)->name('locale.switch');

Route::get('/invoices/{invoice}/print', [\App\Http\Controllers\InvoicePrintController::class, 'show'])
    ->name('invoices.print');
