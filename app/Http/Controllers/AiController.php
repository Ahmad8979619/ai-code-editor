<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\SavedCode;

class AiController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | FORMAT CODE
    |--------------------------------------------------------------------------
    */

    public function format(Request $request)
    {

        try {

            $code = $request->code;

            $language = $request->language;

            $response = Http::withoutVerifying()
                ->timeout(30)
                ->withToken(env('OPENAI_API_KEY'))
                ->post(

                    'https://api.openai.com/v1/responses',

                    [

                        "model" => "gpt-4.1-mini",

                        "input" =>

                            "Format this ".$language." code properly.

Return ONLY formatted code.
Do NOT add explanation.
Do NOT add markdown.

".$code

                    ]

                );

            if (!$response->successful()) {

                return response()->json([

                    'result' => 'OpenAI API Error'

                ], 500);
            }

            $data = $response->json();

            $result =

                $data['output'][0]['content'][0]['text']
                ?? $code;

            $clean = preg_replace('/```[a-z]*\n?/i', '', $result);

            $clean = str_replace('```', '', $clean);

            $clean = trim($clean);

            return response()->json([

                'result' => $clean

            ]);

        } catch (\Exception $e) {

            return response()->json([

                'result' => 'ERROR: '.$e->getMessage()

            ], 500);
        }
    }



    /*
    |--------------------------------------------------------------------------
    | SUGGEST CODE
    |--------------------------------------------------------------------------
    */

    public function suggest(Request $request)
    {

        try {

            $code = $request->code;

            $language = $request->language ?? 'code';

            $response = Http::withoutVerifying()
                ->timeout(30)
                ->withToken(env('OPENAI_API_KEY'))
                ->post(

                    'https://api.openai.com/v1/responses',

                    [

                        "model" => "gpt-4.1-mini",

                        "input" =>

                            "Fix and improve this ".$language." code.

Return ONLY raw code.
Do NOT add explanation.
Do NOT add markdown.
Do NOT add ```.

".$code

                    ]

                );

            if (!$response->successful()) {

                return response()->json([

                    'result' => 'OpenAI API Error'

                ], 500);
            }

            $data = $response->json();

            $result =

                $data['output'][0]['content'][0]['text']
                ?? $code;

            $clean = preg_replace('/```[a-z]*\n?/i', '', $result);

            $clean = str_replace('```', '', $clean);

            $clean = trim($clean);

            if (Auth::check()) {

                SavedCode::create([

                    'user_id' => Auth::id(),

                    'code' => $clean,

                    'language' => $language

                ]);
            }

            return response()->json([

                'result' => $clean

            ]);

        } catch (\Exception $e) {

            return response()->json([

                'result' => 'ERROR: '.$e->getMessage()

            ], 500);
        }
    }



    /*
    |--------------------------------------------------------------------------
    | AUTOCOMPLETE
    |--------------------------------------------------------------------------
    */

    public function autocomplete(Request $request)
    {

        try {

            $code = $request->code;

            $language = $request->language ?? 'code';

            $response = Http::withoutVerifying()
                ->timeout(30)
                ->withToken(env('OPENAI_API_KEY'))
                ->post(

                    'https://api.openai.com/v1/responses',

                    [

                        "model" => "gpt-4.1-mini",

                        "input" =>

                            "Continue this ".$language." code.

Return ONLY the next few words of code.
Do NOT add explanation.
Do NOT add markdown.
Do NOT add ```.

".$code

                    ]

                );

            if (!$response->successful()) {

                return response()->json([

                    'suggestion' => ''

                ]);
            }

            $data = $response->json();

            $text =

                $data['output'][0]['content'][0]['text']
                ?? "";

            $clean = preg_replace('/```[a-z]*\n?/i', '', $text);

            $clean = str_replace('```', '', $clean);

            $clean = trim($clean);

            return response()->json([

                'suggestion' => $clean

            ]);

        } catch (\Exception $e) {

            return response()->json([

                'suggestion' => ''

            ]);
        }
    }

}
