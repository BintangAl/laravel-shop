<?php

use App\Http\Controllers\API\ApiController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/select-address/{key}/{id}', [ApiController::class, 'SelectAddress']);
Route::post('/delivery-option/{key}/{destination}', [ApiController::class, 'DeliveryOption']);

Route::post('/statistic', [ApiController::class, 'Statistic']);
Route::post('/users', [ApiController::class, 'Users']);
Route::post('/products', [ApiController::class, 'Products']);
Route::post('/categories', [ApiController::class, 'CategorieProduct']);
Route::post('/transactions', [ApiController::class, 'Transactions']);

Route::post('/galery', [ApiController::class, 'Galery']);
Route::post('/set-image', [ApiController::class, 'SetImage']);
