<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIController extends Controller
{


    public function Chat(Request $request)
    {

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('OPENROUTER_API_KEY'),
                'Content-Type' => 'application/json',
            ])->post('https://openrouter.ai/api/v1/chat/completions', [
                'model' => 'deepseek/deepseek-chat', // or 'openrouter/auto'
                'messages' => [
                    ['role' => 'user', 'content' => $request->message]
                ],
            ]);

            if ($response->failed()) {
                Log::error('OpenRouter API failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return response()->json([
                    'error' => '❌ API call failed',
                    'status' => $response->status(),
                    'body' => $response->body(),
                ], $response->status());
            }

            $data = $response->json();
            Log::info('OpenRouter full response', $data);

            $assistantReply = data_get($data, 'choices.0.message.content');

            if ($assistantReply) {
                return response()->json(['message' => $assistantReply]);
            } else {
                return response()->json([
                    'message' => '⚠️ AI returned no answer.',
                    'raw' => $data,
                ], 422);
            }
        } catch (\Throwable $e) {
            Log::error('Exception during OpenRouter call', ['error' => $e->getMessage()]);
            return response()->json([
                'error' => '❌ Error reaching assistant.',
                'exception' => $e->getMessage(),
            ], 500);
        }
    }
}
