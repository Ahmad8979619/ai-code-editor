<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AiController;
use App\Models\SavedCode;

Route::get('/', function () {
    return redirect('/editor');
});


Route::get('/editor', function () {

    return view('editor');

})->middleware('auth');


Route::post('/suggest',

[AiController::class,'suggest']

)->middleware('auth');


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

            $output =
            "Run currently supported for PHP only";

        }

        return response()->json([

            'output'=>$output

        ]);

    }
    catch(\Throwable $e){

        return response()->json([

            'error'=>$e->getMessage()

        ]);

    }

})->middleware('auth');


Route::get('/history', function(){

    $codes =
    SavedCode::where(

        'user_id',
        auth()->id()

    )->latest()->get();

    return view(

        'history',

        compact('codes')

    );

})->middleware('auth');


require __DIR__.'/auth.php';
