<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\GameController;
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::prefix('v1')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('/signup', [AuthController::class, 'signup']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    });

});


Route::prefix('v1/game')->middleware('auth:sanctum')->controller(GameController::class)->group(function () {
    Route::post('/', 'createGame');
    Route::post('/join', 'joinGame');
    Route::post('/leave', 'leaveGame');
    Route::get('/{code}/qr', 'generateGameQr');
    Route::post('/{game}/kick', 'kickPlayer');
    Route::get('/{id}', 'getGameDetails');
    Route::post('/{id}/start', 'startGame');
    Route::get('/current', 'getMyCurrentGame');
});
