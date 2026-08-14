<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ChatMessageController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymentController;
use App\Http\Middleware\EnsureAdminUser;
use Illuminate\Support\Facades\Route;

Route::get('/health', HealthController::class)->name('health');

Route::get('/', HomeController::class)->name('home');
Route::get('/services', [HomeController::class, 'services'])->name('services.index');
Route::get('/products', [HomeController::class, 'products'])->name('products.index');
Route::get('/industries', [HomeController::class, 'industries'])->name('industries.index');
Route::get('/work', [HomeController::class, 'work'])->name('work.index');
Route::get('/about', [HomeController::class, 'about'])->name('about.index');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact.index');
Route::get('/products/navkwa-build', [HomeController::class, 'navkwaBuild'])->name('products.navkwa-build');
Route::get('/products/navkwa-build/payment/{plan?}', [PaymentController::class, 'navkwaBuildCheckout'])->name('products.navkwa-build.payment');
Route::view('/privacy-policy', 'legal.page', [
    'title' => 'Privacy Policy',
    'summary' => 'This page outlines how Navkwa Group Ltd. handles enquiries, contact information, uploaded files, payment records, and website usage data.',
    'items' => [
        'We collect information you submit through contact, consultation, payment, and chat forms.',
        'We use enquiry information to respond, scope services, manage support, and maintain business records.',
        'Uploaded files are used only to review the request they relate to, unless another arrangement is agreed.',
        'Payment records are stored for transaction tracking and administrative follow-up; card details are not stored by Navkwa.',
        'Formal project contracts may include additional confidentiality, data handling, and security terms.',
    ],
])->name('legal.privacy');
Route::view('/terms-of-use', 'legal.page', [
    'title' => 'Terms of Use',
    'summary' => 'These terms explain the basic conditions for using the Navkwa website and starting a business enquiry.',
    'items' => [
        'Website content is provided for general information and does not create a service contract by itself.',
        'Project scope, pricing, delivery timelines, ownership, support, and confidentiality are confirmed in written agreements.',
        'Visitors should not misuse forms, chat, payment links, or administrative endpoints.',
        'Product information may change as platforms are developed, improved, or commercially launched.',
        'Navkwa may update these terms as services, products, and policies evolve.',
    ],
])->name('legal.terms');
Route::view('/cookie-policy', 'legal.page', [
    'title' => 'Cookie Policy',
    'summary' => 'This page explains how cookies and similar browser storage may be used on the Navkwa website.',
    'items' => [
        'Essential cookies may be used for security, form handling, sessions, and dashboard authentication.',
        'Local browser storage may be used to keep chat context or interface preferences on a device.',
        'Analytics or marketing cookies should only be enabled when configured and disclosed before launch.',
        'You can control cookies through your browser settings.',
        'Future product portals may have product-specific cookie and privacy notices.',
    ],
])->name('legal.cookies');

Route::post('/contact', [ContactMessageController::class, 'store'])->name('contact.store');

Route::get('/chat/messages', [ChatMessageController::class, 'index'])->name('chat.messages.index');
Route::post('/chat/messages', [ChatMessageController::class, 'store'])->name('chat.messages.store');

// PAYMENT INTEGRATION ROUTES:
// Clients begin at /payments. Provider callbacks/webhooks return here after
// Hubtel or Paystack processes Mobile Money or card checkout.
Route::get('/payments', [PaymentController::class, 'create'])->name('payments.create');
Route::post('/payments/initialize', [PaymentController::class, 'initialize'])->name('payments.initialize');
Route::get('/payments/paystack/callback', [PaymentController::class, 'paystackCallback'])->name('payments.paystack.callback');
Route::post('/payments/paystack/webhook', [PaymentController::class, 'paystackWebhook'])->name('payments.paystack.webhook');
Route::get('/payments/hubtel/callback', [PaymentController::class, 'hubtelCallback'])->name('payments.hubtel.callback');
Route::post('/payments/hubtel/webhook', [PaymentController::class, 'hubtelWebhook'])->name('payments.hubtel.webhook');

Route::get('/admin/login', [AdminAuthController::class, 'create'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'store'])->name('admin.login.store');
Route::post('/admin/logout', [AdminAuthController::class, 'destroy'])->middleware('auth')->name('admin.logout');

Route::prefix('admin')->middleware(['auth', EnsureAdminUser::class])->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/enquiries', [AdminController::class, 'enquiries'])->name('enquiries.index');
    Route::get('/enquiries/export', [AdminController::class, 'exportEnquiries'])->name('enquiries.export');
    Route::patch('/enquiries/{contactMessage}', [AdminController::class, 'updateEnquiry'])->name('enquiries.update');
    Route::delete('/enquiries/{contactMessage}', [AdminController::class, 'destroyEnquiry'])->name('enquiries.destroy');
    Route::post('/enquiries/{contactMessage}/convert-lead', [AdminController::class, 'convertEnquiryToLead'])->name('enquiries.convert-lead');

    Route::get('/leads', [AdminController::class, 'leads'])->name('leads.index');
    Route::post('/leads', [AdminController::class, 'storeLead'])->name('leads.store');
    Route::patch('/leads/{lead}', [AdminController::class, 'updateLead'])->name('leads.update');

    Route::get('/consultations', [AdminController::class, 'consultations'])->name('consultations.index');
    Route::post('/consultations', [AdminController::class, 'storeConsultation'])->name('consultations.store');
    Route::patch('/consultations/{consultation}', [AdminController::class, 'updateConsultation'])->name('consultations.update');

    Route::get('/content', [AdminController::class, 'content'])->name('content.index');
    Route::post('/content', [AdminController::class, 'storeContent'])->name('content.store');
    Route::patch('/content/{contentItem}', [AdminController::class, 'updateContent'])->name('content.update');

    Route::get('/support', [AdminController::class, 'support'])->name('support.index');
    Route::get('/live-chats', [AdminController::class, 'liveChats'])->name('live-chats.index');
    Route::get('/payments', [AdminController::class, 'payments'])->name('payments.index');

    Route::get('/marketing', [AdminController::class, 'marketing'])->name('marketing.index');
    Route::post('/subscribers', [AdminController::class, 'storeSubscriber'])->name('subscribers.store');
    Route::patch('/subscribers/{subscriber}', [AdminController::class, 'updateSubscriber'])->name('subscribers.update');

    Route::get('/careers', [AdminController::class, 'careers'])->name('careers.index');
    Route::post('/jobs', [AdminController::class, 'storeJob'])->name('jobs.store');
    Route::patch('/jobs/{jobOpening}', [AdminController::class, 'updateJob'])->name('jobs.update');
    Route::post('/applications', [AdminController::class, 'storeApplication'])->name('applications.store');
    Route::patch('/applications/{jobApplication}', [AdminController::class, 'updateApplication'])->name('applications.update');

    Route::patch('/chat/{sessionId}', [AdminController::class, 'updateChatSession'])->name('chat.update');
    Route::post('/chat/{sessionId}/reply', [AdminController::class, 'storeChatReply'])->name('chat.reply');

    Route::get('/management', [AdminController::class, 'management'])->name('management.index');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::patch('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');

    Route::get('/system', [AdminController::class, 'system'])->name('system.index');
    Route::patch('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
});
