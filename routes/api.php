<?php

use App\Http\Controllers\ConversationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MessagesController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
//Route::middleware('auth:sanctum')->group(function () {
    Route::get('/conversations',[ConversationController::class,'index']);
    Route::get('/conversations/{conversation}',[ConversationController::class,'participants']);
    Route::post('/conversations/{conversation}/participants',[ConversationController::class,'AddParticipants']);
    Route::delete('/conversations/{conversation}/participants',[ConversationController::class,'RemoveParticipants']);


Route::get('/conversations/{id}/message',[MessagesController::class,'index']);
    Route::post('message',[MessagesController::class,'store']);
    Route::delete('message/{id}',[MessagesController::class,'destroy']);
//});
