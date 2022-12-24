<?php

use App\Http\Controllers\V1\AlbumController;
use App\Http\Controllers\V1\ImageManipunationController;
use App\Models\ImageManupulation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Termwind\Components\Raw;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::prefix('v1')->group(function()
{
    Route::apiResource('album',AlbumController::class);
    Route::get('image',[ImageManipunationController::class,'index']);
    Route::get('image/by-album/{album}',[ImageManipunationController::class,'getByAlbum']);
    Route::get('image/{image}',[ImageManipunationController::class,'show']);
    Route::post('image/resize',[ImageManipunationController::class,'resize']);
    Route::delete('image/{image}',[ImageManipunationController::class,'destroy']);
});