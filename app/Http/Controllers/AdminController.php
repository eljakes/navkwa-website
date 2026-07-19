<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\ContactMessage;

class AdminController extends Controller
{
    public function __invoke()
    {
        $contactMessages = ContactMessage::latest()->take(30)->get();
        $chatSessions = ChatMessage::query()
            ->latest()
            ->get()
            ->groupBy('session_id');

        return view('admin.inbox', [
            'contactMessages' => $contactMessages,
            'chatSessions' => $chatSessions,
        ]);
    }
}
