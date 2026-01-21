<?php

namespace App\Http\Controllers\Api;

use App\Service\GeminiService;

class GeminiController
{
    public function __construct(
        private GeminiService $geminiService
    ){}

    public function chat() {
        $response = $this->geminiService->chatService('Hello, how are you?', []);

        return response()->json($response);
    }
}
