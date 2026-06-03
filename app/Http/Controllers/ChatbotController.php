<?php
namespace App\Http\Controllers;

use App\Models\ChatbotConversation;

class ChatbotController extends Controller
{
    public function index()
    {
        $conversations =
            ChatbotConversation::where(
                'user_id',
                auth()->id()
            )->latest()->get();

        return view(
            'dashboard.chatbot',
            compact('conversations')
        );
    }
}