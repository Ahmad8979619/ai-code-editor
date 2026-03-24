<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AiController;

Route::post('/suggest', [AiController::class, 'suggest']);
