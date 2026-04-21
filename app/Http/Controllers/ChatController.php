<?php

namespace App\Http\Controllers;

use App\Services\ChatService;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function send(Request $request, ChatService $chatService)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'history' => 'nullable|array',
            'cart' => 'nullable|array',
            'customer' => 'nullable|array',
        ]);

        $response = $chatService->chat(
            $request->input('message'),
            [
                'history' => $request->input('history', []),
                'cart' => $request->input('cart', []),
                'customer' => $request->input('customer', []),
                'locale' => app()->getLocale(),
            ]
        );

        return response()->json($response);
    }

    public function widget()
    {
        return view('components.chat-widget');
    }
}
