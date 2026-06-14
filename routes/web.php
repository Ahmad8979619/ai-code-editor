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
| الصفحة الرئيسية — تعرض welcome.blade.php
|--------------------------------------------------------------------------
*/

Route::get('/', function () {

    return view('welcome');

});


/*
|--------------------------------------------------------------------------
| Register
| تسجيل مستخدم جديد
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
| تسجيل الدخول
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
| تسجيل الخروج — يحتاج auth middleware
|--------------------------------------------------------------------------
*/

Route::post('/logout',[

    AuthenticatedSessionController::class,
    'destroy'

])->middleware('auth')->name('logout');


/*
|--------------------------------------------------------------------------
| Dashboard
| الصفحة الرئيسية بعد الدخول
| تجلب: عدد الكودات، آخر كود، عدد AI requests
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {

    $user = auth()->user();

    // عدد الكودات المحفوظة للمستخدم
    $totalFiles = SavedCode::where(
        'user_id',
        $user->id
    )->count();

    // آخر كود محفوظ
    $lastCode = SavedCode::where(
        'user_id',
        $user->id
    )->latest()->first();

    // عدد مرات استخدام الـ AI
    $totalAiUsage = SavedCode::where(
        'user_id',
        $user->id
    )->count();

    return view(
        'dashboard',
        compact(
            'user',
            'totalFiles',
            'lastCode',
            'totalAiUsage'
        )
    );

})->middleware('auth')->name('dashboard');


/*
|--------------------------------------------------------------------------
| Editor
| صفحة المحرر الرئيسية
|--------------------------------------------------------------------------
*/

Route::get('/editor', function () {

    return view('editor');

})->middleware('auth')->name('editor');


/*
|--------------------------------------------------------------------------
| AI Routes — مع Rate Limiting
|  تحسين الأمان: 30 طلب كل دقيقة لكل مستخدم
| لو تجاوز الحد يرجع 429 Too Many Requests
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'throttle:30,1'])->group(function () {

    /*
    | AI Suggest
    | يصلح الكود ويحسّنه عن طريق OpenAI
    */
    Route::post('/suggest',

        [AiController::class, 'suggest']

    )->name('suggest');


    /*
    | AI Format
    | ينسّق الكود تلقائياً
    */
    Route::post('/format',

        [AiController::class, 'format']

    )->name('format');


    /*
    | AI Autocomplete
    | يكمل الكود أثناء الكتابة
    */
    Route::post('/autocomplete',

        [AiController::class, 'autocomplete']

    )->name('autocomplete');


    /*
    | AI Explain
    | يشرح الكود بلغة بسيطة
    */
    Route::post('/explain',

        [AiController::class, 'explain']

    )->name('explain');


    /*
    | AI Chat
    | محادثة مع الـ AI عن الكود الحالي
    */
    Route::post('/chat',

        [AiController::class, 'chat']

    )->name('chat');

});


/*
|--------------------------------------------------------------------------
| Run Code — Judge0 API
|  تحسين الأمان: throttle 20 مرة كل دقيقة
| الكود ينفّذ على سيرفر Judge0 الخارجي — مش على سيرفرنا
| هذا يحمي السيرفر من الأكواد الخطيرة (sandbox معزول)
|--------------------------------------------------------------------------
*/

Route::post('/run', function (Request $request) {

    $code  = $request->code;
    $lang  = strtolower($request->language);
    $input = $request->input('input', ''); // stdin للبرامج التي تستخدم input()

    /*
    | Judge0 Language IDs
    | كل لغة لها رقم خاص في Judge0 API
    */
    $languages = [
        'python'     => 71,
        'javascript' => 63,
        'typescript' => 63,
        'php'        => 68,
        'java'       => 62,
        'cpp'        => 54,
        'c++'        => 54,
        'c'          => 50,
        'csharp'     => 51,
        'c#'         => 51,
        'go'         => 60,
        'rust'       => 73,
        'html'       => 43,
        'css'        => 43,
        'json'       => 43,
    ];

    // إذا اللغة مش موجودة في القائمة، افتراضياً Python
    $language_id = $languages[$lang] ?? 71;

    // إرسال الكود إلى Judge0 API للتنفيذ
    $response = Http::post(

        'https://ce.judge0.com/submissions?base64_encoded=false&wait=true',

        [
            'source_code' => $code,
            'language_id' => $language_id,
            'stdin'       => $input, // يمرر الـ input للبرنامج
        ]

    );

    $data = $response->json();

    // نأخذ الـ output — stdout أولاً، ثم stderr، ثم compile errors
    $output =
        $data['stdout']
        ?? $data['stderr']
        ?? $data['compile_output']
        ?? 'No output';

    return response()->json([
        'output' => $output
    ]);

//  تحسين الأمان: throttle على الـ run — 20 تنفيذ كل دقيقة
})->middleware(['auth', 'throttle:20,1'])->name('run');


/*
|--------------------------------------------------------------------------
| History
|  تحسين: paginate(10) بدل get()
| يعرض 10 كودات في كل صفحة — يمنع بطء الصفحة مع الكودات الكثيرة
|--------------------------------------------------------------------------
*/

Route::get('/history', function () {

    $codes = SavedCode::where(

        'user_id',

        auth()->id()

    )->latest()->paginate(10); // ✅ pagination بدل get()

    return view(

        'history',

        compact('codes')

    );

})->middleware('auth')->name('history');


/*
|--------------------------------------------------------------------------
| Delete Code
|  تحسين الأمان: Authorization — يحذف فقط إذا كان الكود يخص المستخدم
| firstOrFail() ترجع 404 إذا حاول شخص يحذف كود شخص ثاني
|--------------------------------------------------------------------------
*/

Route::delete('/delete-code/{id}', function ($id) {

    //  Authorization: نتحقق إن user_id يطابق المستخدم الحالي
    $code = SavedCode::where('id', $id)
                     ->where('user_id', auth()->id())
                     ->firstOrFail(); // 404 إذا مش موجود أو مش ملكه

    $code->delete();

    //  Flash message تظهر في الصفحة بعد الحذف
    return back()->with('success', 'Code deleted successfully!');

})->middleware('auth')->name('delete.code');
