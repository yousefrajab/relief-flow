<?php

namespace App\Http\Controllers;

use App\Services\AIService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssistantController extends Controller
{
    public function ask(Request $request, AIService $aiService): JsonResponse
    {
        $request->validate([
            'message' => ['required', 'string', 'max:500'],
        ]);

        $reply = $aiService->answerAssistantQuestion($request->message, Auth::user());

        return response()->json(['reply' => $reply]);
    }
}
