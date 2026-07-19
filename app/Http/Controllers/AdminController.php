<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\ContactMessage;
use App\Models\PaymentTransaction;

class AdminController extends Controller
{
    public function __invoke()
    {
        $contactMessages = ContactMessage::latest()->take(30)->get();
        $payments = PaymentTransaction::latest()->take(30)->get();
        $chatSessions = ChatMessage::query()
            ->latest()
            ->get()
            ->groupBy('session_id');

        return view('admin.inbox', [
            'contactMessages' => $contactMessages,
            'payments' => $payments,
            'chatSessions' => $chatSessions,
        ]);
    }
}
