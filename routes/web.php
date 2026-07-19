<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ChatMessageController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::post('/contact', [ContactMessageController::class, 'store'])->name('contact.store');

Route::get('/chat/messages', [ChatMessageController::class, 'index'])->name('chat.messages.index');
Route::post('/chat/messages', [ChatMessageController::class, 'store'])->name('chat.messages.store');

// PAYMENT INTEGRATION ROUTES:
// Clients begin at /payments. Provider callbacks/webhooks return here after
// Hubtel or Paystack processes Mobile Money or card checkout.
Route::get('/payments', [PaymentController::class, 'create'])->name('payments.create');
Route::post('/payments/initialize', [PaymentController::class, 'initialize'])->name('payments.initialize');
Route::get('/payments/demo/{payment}', [PaymentController::class, 'demo'])->name('payments.demo');
Route::get('/payments/paystack/callback', [PaymentController::class, 'paystackCallback'])->name('payments.paystack.callback');
Route::post('/payments/paystack/webhook', [PaymentController::class, 'paystackWebhook'])->name('payments.paystack.webhook');
Route::get('/payments/hubtel/callback', [PaymentController::class, 'hubtelCallback'])->name('payments.hubtel.callback');
Route::post('/payments/hubtel/webhook', [PaymentController::class, 'hubtelWebhook'])->name('payments.hubtel.webhook');

Route::get('/admin', AdminController::class)->name('admin.inbox');
