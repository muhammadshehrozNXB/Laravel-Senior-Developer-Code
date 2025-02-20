<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TranslationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::post('login', function (Request $request) {
    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        $user = Auth::user();

        return response()->json([
            'token' => $user->createToken('laravelTest')->plainTextToken,
        ]);
    }

    return response()->json([
        'message' => 'Unauthorized',
    ], 401);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('translations', TranslationController::class);
    Route::controller(TranslationController::class)->group(function () {
        Route::get('translations-search', 'search');
        Route::get('translations-export', 'exportTranslations');
    });
});

