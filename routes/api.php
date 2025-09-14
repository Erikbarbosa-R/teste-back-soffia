<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;

Route::get('health', function () {
    return response()->json(['status' => 'ok', 'timestamp' => now()], 200);
});

Route::get('ping', function () {
    return response()->json(['pong' => true], 200);
});

Route::post('test-register', function (Request $request) {
    return response()->json([
        'message' => 'Teste de register funcionando',
        'data' => $request->all()
    ], 200);
});

Route::post('test-validation', function (Request $request) {
    try {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|string|min:6',
            'telefone' => 'nullable|string|max:20',
        ]);
        
        return response()->json([
            'message' => 'Validação funcionando',
            'validated' => $validated
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Erro na validação',
            'error' => $e->getMessage()
        ], 400);
    }
});

Route::post('test-simple', function (Request $request) {
    return response()->json([
        'message' => 'Rota simples funcionando',
        'method' => $request->method(),
        'content_type' => $request->header('Content-Type'),
        'data' => $request->all()
    ], 200);
});

Route::get('test-auth', function (Request $request) {
    return response()->json([
        'message' => 'Teste de autenticação',
        'headers' => $request->headers->all(),
        'auth_header' => $request->header('Authorization'),
        'user' => auth('api')->user()
    ], 200);
});

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
});

Route::middleware(['jwt.auth'])->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
        Route::post('refresh', [AuthController::class, 'refresh']);
    });

    Route::apiResource('users', UserController::class);

    Route::apiResource('posts', PostController::class);

    Route::post('posts/{post}/comments', [PostController::class, 'addComment']);
    Route::delete('posts/{post}/comments/{comment}', [PostController::class, 'deleteComment']);
});
