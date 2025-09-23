<?php

namespace App\Http\Controllers\Mono;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChatMessage;

class ChatLogController extends Controller
{
   public function index()
    {
        $sessions = ChatMessage::select('session_id')
            ->groupBy('session_id')
            ->latest()
            ->get();

        $chats = [];
        foreach ($sessions as $session) {
            $chats[$session->session_id] = ChatMessage::where('session_id', $session->session_id)->get();
        }

        return view('admin.chat.index', compact('chats'));
    }

}
