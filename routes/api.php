<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\DashboardController;

/**
 * @OA\Get(
 *     path="/api/health",
 *     summary="Health Check",
 *     description="Verifica se a API está funcionando",
 *     tags={"Utilitários"},
 *     @OA\Response(
 *         response=200,
 *         description="API funcionando",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="ok"),
 *             @OA\Property(property="timestamp", type="string", example="2023-01-01T00:00:00.000000Z")
 *         )
 *     )
 * )
 */
Route::get('health', function () {
    return response()->json(['status' => 'ok', 'timestamp' => now()], 200);
});

/**
 * @OA\Get(
 *     path="/api/ping",
 *     summary="Ping",
 *     description="Resposta simples de ping",
 *     tags={"Utilitários"},
 *     @OA\Response(
 *         response=200,
 *         description="Pong",
 *         @OA\JsonContent(
 *             @OA\Property(property="pong", type="boolean", example=true)
 *         )
 *     )
 * )
 */
Route::get('ping', function () {
    return response()->json(['pong' => true], 200);
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
    Route::apiResource('tags', TagController::class);

    Route::post('posts/{post}/comments', [PostController::class, 'addComment']);
    Route::delete('posts/{post}/comments/{comment}', [PostController::class, 'deleteComment']);

    // Dashboard routes
    Route::prefix('dashboard')->group(function () {
        Route::get('stats', [DashboardController::class, 'stats']);
        Route::get('activity', [DashboardController::class, 'activity']);
    });
});
