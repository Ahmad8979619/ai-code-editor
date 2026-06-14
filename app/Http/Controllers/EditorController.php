<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EditorController extends Controller
{
    public function chat(Request $request)
{
    $message  = $request->input('message', '');
    $code     = $request->input('code', '');
    $language = $request->input('language', 'python');

    $client = new \GuzzleHttp\Client();

    $systemPrompt = "You are an expert programming assistant inside a code editor called Stackly.
Answer questions about the user's code. Be concise and direct.
When suggesting fixes, always show the corrected code.";

    $userMessage = "Language: {$language}\n\nCode:\n```{$language}\n{$code}\n```\n\nQuestion: {$message}";

    try {
        $response = $client->post('https://api.anthropic.com/v1/messages', [
            'headers' => [
                'x-api-key'         => env('ANTHROPIC_API_KEY'),
                'anthropic-version' => '2023-06-01',
                'Content-Type'      => 'application/json',
            ],
            'json' => [
                'model'      => 'claude-opus-4-6',
                'max_tokens' => 1024,
                'system'     => $systemPrompt,
                'messages'   => [
                    ['role' => 'user', 'content' => $userMessage]
                ],
            ],
        ]);

        $data   = json_decode($response->getBody(), true);
        $result = $data['content'][0]['text'] ?? 'No response';

        return response()->json(['result' => $result]);

    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}
}
