<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\SavedCode;
use Illuminate\Support\Facades\Auth;

class AiController extends Controller
{

public function suggest(Request $request)
{

$code = $request->input('code');

$language = $request->input('language');

$response = Http::withToken(env('OPENAI_API_KEY'))
->post('https://api.openai.com/v1/responses',[

"model"=>"gpt-4.1-mini",

"input"=>
"Continue and fix this ".$language." code.
Return ONLY the completed code:

".$code

]);

$data = $response->json();

$result =
$data['output'][0]['content'][0]['text']
?? "No response";


if(Auth::check()){

SavedCode::create([

'user_id'=>Auth::id(),

'code'=>$result,

'language'=>$language

]);

}

return response()->json([

'result'=>$result

]);

}

}
