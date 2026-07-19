<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ChatMessageController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::post('/contact', [ContactMessageController::class, 'store'])->name('contact.store');

Route::get('/chat/messages', [ChatMessageController::class, 'index'])->name('chat.messages.index');
Route::post('/chat/messages', [ChatMessageController::class, 'store'])->name('chat.messages.store');

Route::get('/admin', AdminController::class)->name('admin.inbox');
