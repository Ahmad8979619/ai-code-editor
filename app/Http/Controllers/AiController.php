<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\SavedCode;
use Illuminate\Support\Facades\Auth;

class AiController extends Controller
{

/*
FORMAT CODE
*/

public function format(Request $request)
{

$code = $request->code;

$language = $request->language;


$response = Http::withToken(env('OPENAI_API_KEY'))

->post(

'https://api.openai.com/v1/responses',

[

"model"=>"gpt-4.1-mini",

"input"=>

"Format this ".$language." code properly.

Return ONLY formatted code.
Do NOT add explanation.
Do NOT add markdown.

".$code

]

);


$data = $response->json();


$result =

$data['output'][0]['content'][0]['text']
?? $code;


/* clean markdown */

$clean = preg_replace('/```[a-z]*\n?/i','',$result);

$clean = str_replace('```','',$clean);

$clean = trim($clean);


return response()->json([

'result'=>$clean

]);

}



/*
SUGGEST CODE
*/

public function suggest(Request $request)
{

$code = $request->code;

$language = $request->language;


$response = Http::withToken(env('OPENAI_API_KEY'))

->post(

'https://api.openai.com/v1/responses',

[

"model"=>"gpt-4.1-mini",

"input"=>

"Fix and improve this ".$language." code.

Return ONLY raw code.
Do NOT add explanation.
Do NOT add markdown.
Do NOT add ```.

".$code

]

);


$data = $response->json();


$result =

$data['output'][0]['content'][0]['text']
?? $code;


/* clean markdown */

$clean = preg_replace('/```[a-z]*\n?/i','',$result);

$clean = str_replace('```','',$clean);

$clean = trim($clean);


/* save */

if(Auth::check()){

SavedCode::create([

'user_id'=>Auth::id(),

'code'=>$clean,

'language'=>$language

]);

}


/* return */

return response()->json([

'result'=>$clean

]);

}



/*
AI AUTOCOMPLETE
*/

public function autocomplete(Request $request)
{

$code = $request->code;

$language = $request->language;


$response = Http::withToken(env('OPENAI_API_KEY'))

->post(

'https://api.openai.com/v1/responses',

[

"model"=>"gpt-4.1-mini",

"input"=>

"Continue this ".$language." code.

Return ONLY the next few words of code.
Do NOT add explanation.
Do NOT add markdown.
Do NOT add ```.

".$code

]

);


$data = $response->json();


$text =

$data['output'][0]['content'][0]['text']
?? "";


/* clean */

$clean = preg_replace('/```[a-z]*\n?/i','',$text);

$clean = str_replace('```','',$clean);

$clean = trim($clean);


return response()->json([

'suggestion'=>$clean

]);

}


}
