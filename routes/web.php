<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\AiController;
use App\Models\SavedCode;


/*
|--------------------------------------------------------------------------
| Landing Page
|--------------------------------------------------------------------------
*/

Route::get('/', function () {

    return view('welcome');

});


/*
|--------------------------------------------------------------------------
| Register
|--------------------------------------------------------------------------
*/

Route::get('/register',[

RegisteredUserController::class,
'create'

])->name('register');


Route::post('/register',[

RegisteredUserController::class,
'store'

]);


/*
|--------------------------------------------------------------------------
| Login
|--------------------------------------------------------------------------
*/

Route::get('/login',[

AuthenticatedSessionController::class,
'create'

])->name('login');


Route::post('/login',[

AuthenticatedSessionController::class,
'store'

]);


/*
|--------------------------------------------------------------------------
| Logout
|--------------------------------------------------------------------------
*/

Route::post('/logout',[

AuthenticatedSessionController::class,
'destroy'

])->middleware('auth')->name('logout');


/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/dashboard',function(){

$user = auth()->user();

$totalFiles = SavedCode::where(
'user_id',
$user->id
)->count();

$lastCode = SavedCode::where(
'user_id',
$user->id
)->latest()->first();

return view(
'dashboard',
compact(
'user',
'totalFiles',
'lastCode'
)
);

})->middleware('auth')->name('dashboard');


/*
|--------------------------------------------------------------------------
| Editor
|--------------------------------------------------------------------------
*/

Route::get('/editor',function(){

return view('editor');

})->middleware('auth')->name('editor');


/*
|--------------------------------------------------------------------------
| AI Suggest (fix code)
|--------------------------------------------------------------------------
*/

Route::post('/suggest',

[AiController::class,'suggest']

)->middleware('auth')->name('suggest');


/*
|--------------------------------------------------------------------------
| AI Format Code
|--------------------------------------------------------------------------
*/

Route::post('/format',

[AiController::class,'format']

)->middleware('auth')->name('format');


/*
|--------------------------------------------------------------------------
| AI Autocomplete
|--------------------------------------------------------------------------
*/

Route::post('/autocomplete',

[AiController::class,'autocomplete']

)->middleware('auth')->name('autocomplete');


/*
|--------------------------------------------------------------------------
| Run Code (Judge0 API)
|--------------------------------------------------------------------------
*/

Route::post('/run',function(Request $request){

$code = $request->code;

$lang = strtolower($request->language);


/*
Judge0 language IDs
*/

$languages = [

'python'=>71,

'javascript'=>63,
'typescript'=>63,

'php'=>68,

'java'=>62,

'cpp'=>54,
'c++'=>54,

'c'=>50,

'csharp'=>51,
'c#'=>51,

'go'=>60,

'rust'=>73,

'html'=>43,
'css'=>43,
'json'=>43

];


/*
default language python
*/

$language_id =

$languages[$lang] ?? 71;


/*
send to judge0
*/

$response = Http::post(

'https://ce.judge0.com/submissions?base64_encoded=false&wait=true',

[

'source_code'=>$code,

'language_id'=>$language_id

]

);


$data = $response->json();


$output =

$data['stdout']
?? $data['stderr']
?? $data['compile_output']
?? 'No output';


return response()->json([

'output'=>$output

]);

})->middleware('auth')->name('run');


/*
|--------------------------------------------------------------------------
| History
|--------------------------------------------------------------------------
*/

Route::get('/history',function(){

$codes = SavedCode::where(

'user_id',

auth()->id()

)->latest()->get();


return view(

'history',

compact('codes')

);

})->middleware('auth')->name('history');


/*
|--------------------------------------------------------------------------
| Delete Code
|--------------------------------------------------------------------------
*/

Route::delete('/delete-code/{id}',function($id){

SavedCode::where(

'id',$id

)->where(

'user_id',

auth()->id()

)->delete();

return back();

})->middleware('auth')->name('delete.code');
