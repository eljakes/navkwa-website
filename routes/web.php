<?php

use App\Http\Controllers\AdminAuthController;
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

Route::get('/admin/login', [AdminAuthController::class, 'create'])->middleware('guest')->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'store'])->middleware('guest')->name('admin.login.store');
Route::post('/admin/logout', [AdminAuthController::class, 'destroy'])->middleware('auth')->name('admin.logout');

Route::prefix('admin')->middleware('auth')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/enquiries/export', [AdminController::class, 'exportEnquiries'])->name('enquiries.export');
    Route::patch('/enquiries/{contactMessage}', [AdminController::class, 'updateEnquiry'])->name('enquiries.update');
    Route::delete('/enquiries/{contactMessage}', [AdminController::class, 'destroyEnquiry'])->name('enquiries.destroy');
    Route::post('/enquiries/{contactMessage}/convert-lead', [AdminController::class, 'convertEnquiryToLead'])->name('enquiries.convert-lead');

    Route::post('/leads', [AdminController::class, 'storeLead'])->name('leads.store');
    Route::patch('/leads/{lead}', [AdminController::class, 'updateLead'])->name('leads.update');

    Route::post('/consultations', [AdminController::class, 'storeConsultation'])->name('consultations.store');
    Route::patch('/consultations/{consultation}', [AdminController::class, 'updateConsultation'])->name('consultations.update');

    Route::post('/content', [AdminController::class, 'storeContent'])->name('content.store');
    Route::patch('/content/{contentItem}', [AdminController::class, 'updateContent'])->name('content.update');

    Route::post('/subscribers', [AdminController::class, 'storeSubscriber'])->name('subscribers.store');
    Route::patch('/subscribers/{subscriber}', [AdminController::class, 'updateSubscriber'])->name('subscribers.update');

    Route::post('/jobs', [AdminController::class, 'storeJob'])->name('jobs.store');
    Route::patch('/jobs/{jobOpening}', [AdminController::class, 'updateJob'])->name('jobs.update');
    Route::post('/applications', [AdminController::class, 'storeApplication'])->name('applications.store');
    Route::patch('/applications/{jobApplication}', [AdminController::class, 'updateApplication'])->name('applications.update');

    Route::patch('/chat/{sessionId}', [AdminController::class, 'updateChatSession'])->name('chat.update');

    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::patch('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::patch('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
});
