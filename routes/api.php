<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});
Route::post('completion', [\App\Http\Controllers\ChatGptController::class, 'completion']);
Route::post('chat-completion', [\App\Http\Controllers\ChatGptController::class, 'chatCompletion']);
Route::post('edits', [\App\Http\Controllers\ChatGptController::class, 'edits']);
Route::post('models-list', [\App\Http\Controllers\ChatGptController::class, 'getModelsList']);
Route::post('models/{name}', [\App\Http\Controllers\ChatGptController::class, 'getModelByName']);
Route::post('image-generate', [\App\Http\Controllers\ChatGptController::class, 'generateImage']);
Route::post('moderations', [\App\Http\Controllers\ChatGptController::class, 'moderations']);
Route::post('audio-translations', [\App\Http\Controllers\ChatGptController::class, 'audioTranslation']);
