<?php

use Illuminate\Support\Facades\Route;
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

Route::get('/register', [

    RegisteredUserController::class, 'create'

])->name('register');


Route::post('/register', [

    RegisteredUserController::class, 'store'

]);


/*
|--------------------------------------------------------------------------
| Login
|--------------------------------------------------------------------------
*/

Route::get('/login', [

    AuthenticatedSessionController::class, 'create'

])->name('login');


Route::post('/login', [

    AuthenticatedSessionController::class, 'store'

]);


/*
|--------------------------------------------------------------------------
| Logout
|--------------------------------------------------------------------------
*/

Route::post('/logout', [

    AuthenticatedSessionController::class, 'destroy'

])->middleware('auth');


/*
|--------------------------------------------------------------------------
| Editor
|--------------------------------------------------------------------------
*/

Route::get('/editor', function(){

    return view('editor');

})->middleware('auth');


/*
|--------------------------------------------------------------------------
| AI Suggest
|--------------------------------------------------------------------------
*/

Route::post('/suggest', [

    AiController::class, 'suggest'

])->middleware('auth');


/*
|--------------------------------------------------------------------------
| Run Code
|--------------------------------------------------------------------------
*/

Route::post('/run', function(){

    $code = request('code');

    $language = request('language');

    try{

        if($language == "php"){

            ob_start();

            eval($code);

            $output = ob_get_clean();

        }
        else{

            $output = "Run currently supported for PHP only";

        }

        return response()->json([

            'output' => $output

        ]);

    }
    catch(\Throwable $e){

        return response()->json([

            'error' => $e->getMessage()

        ]);

    }

})->middleware('auth');


/*
|--------------------------------------------------------------------------
| History
|--------------------------------------------------------------------------
*/

Route::get('/history', function(){

    $codes = SavedCode::where(

        'user_id',
        auth()->id()

    )->latest()->get();

    return view(

        'history',

        compact('codes')

    );

})->middleware('auth');


/*
|--------------------------------------------------------------------------
| Redirect after login
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function(){

    return redirect('/editor');

})->middleware('auth');
