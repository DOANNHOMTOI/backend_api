<?php

use App\Http\Controllers\API\ClusterController;
use App\Http\Controllers\API\RegionController;
use App\Http\Controllers\API\WPRegionController;
use App\Http\Controllers\API\WPServerController;
use App\Http\Controllers\API\WPClusterController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\WPSizeController;
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

Route::post('register', [UserController::class, 'register']);
Route::post('/changePassWord', [UserController::class, 'changePassWord']);
Route::post('login', [UserController::class, 'login']);

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// API ADMIN
Route::group(['middleware' => 'jwt.auth'], function () {
    Route::post('/validateToken', [UserController::class, 'validateToken']);

    Route::post('/changePassWord', [UserController::class, 'changePassWord']);

    Route::post('/upload', [\App\Http\Controllers\API\UploadFileController::class, 'upload']);

    Route::get('/product-category', [\App\Http\Controllers\API\ProductCategoryController::class, 'index']);
    Route::get('/product-category/{id}', [\App\Http\Controllers\API\ProductCategoryController::class, 'detail']);
    Route::post('/product-category', [\App\Http\Controllers\API\ProductCategoryController::class, 'store']);
    Route::put('/product-category/{id}', [\App\Http\Controllers\API\ProductCategoryController::class, 'update']);
    Route::delete('/product-category/{id}', [\App\Http\Controllers\API\ProductCategoryController::class, 'delete']);

    Route::get('/voucher', [\App\Http\Controllers\API\VoucherController::class, 'index']);
    Route::get('/voucher/{id}', [\App\Http\Controllers\API\VoucherController::class, 'detail']);
    Route::post('/voucher', [\App\Http\Controllers\API\VoucherController::class, 'store']);
    Route::put('/voucher/{id}', [\App\Http\Controllers\API\VoucherController::class, 'update']);
    Route::delete('/voucher/{id}', [\App\Http\Controllers\API\VoucherController::class, 'delete']);



    Route::get('/clusters', [ClusterController::class, 'index']);
    Route::get('/clusters/{id}', [ClusterController::class, 'detail']);
    Route::post('/clusters', [ClusterController::class, 'store']);
    Route::delete('/clusters/{id}', [ClusterController::class, 'delete']);

    Route::get('/regions', [RegionController::class, 'index']);
    Route::get('/regions/{id}', [RegionController::class, 'detail']);
    Route::post('/regions', [RegionController::class, 'store']);
    Route::delete('/regions/{id}', [RegionController::class, 'delete']);

    // == WP
    Route::get('/wp/regions', [WPRegionController::class, 'index']);
    Route::get('/wp/regions/{id}', [WPRegionController::class, 'detail']);
    Route::post('/wp/regions', [WPRegionController::class, 'store']);
    Route::delete('/wp/regions/{id}', [WPRegionController::class, 'delete']);

    Route::get('/wp/servers', [WPServerController::class, 'index']);
    Route::get('/wp/servers/{id}', [WPServerController::class, 'detail']);
    Route::post('/wp/servers', [WPServerController::class, 'store']);
    Route::delete('/wp/servers/{id}', [WPServerController::class, 'delete']);

    Route::get('/wp/clusters', [WPClusterController::class, 'index']);
    Route::get('/wp/clusters/{id}', [WPClusterController::class, 'detail']);
    Route::post('/wp/clusters', [WPClusterController::class, 'store']);
    Route::delete('/wp/clusters/{id}', [WPClusterController::class, 'delete']);

    Route::get('/wp/sizes', [WPSizeController::class, 'index']);
    Route::get('/wp/sizes/{id}', [WPSizeController::class, 'detail']);
    Route::post('/wp/sizes', [WPSizeController::class, 'store']);
    Route::delete('/wp/sizes/{id}', [WPSizeController::class, 'delete']);
});
Route::post('/changePassWord', [UserController::class, 'changePassWord']);
