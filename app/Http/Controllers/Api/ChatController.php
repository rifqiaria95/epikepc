<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use App\Models\ChatMessage;
use App\Models\Knowledge;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function handle(Request $request)
    {
        $message = $request->input('message');
        $sessionId = $request->input('session_id') ?? Str::uuid();

        try {
            // Simpan pesan user
            ChatMessage::create([
                'session_id' => $sessionId,
                'role' => 'user',
                'message' => $message,
            ]);

            // Cari knowledge paling relevan (contoh sederhana: pakai LIKE)
            $relatedKnowledge = Knowledge::where('question', 'LIKE', '%' . $message . '%')->first();

            // Bangun context prompt
            $systemPrompt = "You are a helpful assistant for a bansos application. 
You must only answer based on the following knowledge. If you're not sure, say you don't know.\n\n";

            if ($relatedKnowledge) {
                $systemPrompt .= "Knowledge:\nQ: {$relatedKnowledge->question}\nA: {$relatedKnowledge->answer}";
            } else {
                $systemPrompt .= "No relevant knowledge found.";
            }

            // Kirim ke OpenRouter
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('OPENROUTER_API_KEY'),
                'HTTP-Referer' => 'http://localhost',
                'X-Title' => 'MyChatbot Laravel',
            ])->post('https://openrouter.ai/api/v1/chat/completions', [
                'model' => 'openai/gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $message],
                ],
            ]);

            if ($response->failed()) {
                Log::error('OpenRouter Error:', ['response' => $response->body()]);
                return response()->json([
                    'reply' => 'Sorry, something went wrong with OpenRouter.',
                    'session_id' => $sessionId
                ]);
            }

            $reply = $response->json('choices.0.message.content');

            // Simpan balasan bot
            ChatMessage::create([
                'session_id' => $sessionId,
                'role' => 'bot',
                'message' => $reply,
            ]);

            return response()->json([
                'reply' => $reply,
                'session_id' => $sessionId,
            ]);
        } catch (\Exception $e) {
            Log::error('ChatController Exception:', ['message' => $e->getMessage()]);
            return response()->json([
                'reply' => 'Sorry, something went wrong.',
                'session_id' => $sessionId
            ]);
        }
    }
}
