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

    Route::get('/product', [\App\Http\Controllers\API\ProductController::class, 'index']);
    Route::get('/product/{id}', [\App\Http\Controllers\API\ProductController::class, 'detail']);
    Route::post('/product', [\App\Http\Controllers\API\ProductController::class, 'store']);
    Route::put('/product/{id}', [\App\Http\Controllers\API\ProductController::class, 'update']);
    Route::delete('/product/{id}', [\App\Http\Controllers\API\ProductController::class, 'delete']);

    Route::get('/permission', [\App\Http\Controllers\API\PermissionController::class, 'index']);
    Route::get('/permission/{id}', [\App\Http\Controllers\API\PermissionController::class, 'detail']);
    Route::post('/permission', [\App\Http\Controllers\API\PermissionController::class, 'store']);
    Route::put('/permission/{id}', [\App\Http\Controllers\API\PermissionController::class, 'update']);
    Route::delete('/permission/{id}', [\App\Http\Controllers\API\PermissionController::class, 'delete']);
});

Route::group(['prefix' => 'web'], function () {
    Route::get('/product-category', [\App\Http\Controllers\API\ProductCategoryController::class, 'index']);
    Route::get('/productFilter', [\App\Http\Controllers\API\ProductController::class, 'productFilter']);
    Route::get('/productNews', [\App\Http\Controllers\API\ProductController::class, 'productNews']);
    Route::get('/productDetail/{id}', [\App\Http\Controllers\API\ProductController::class, 'productDetail']);
    Route::post('/order', [\App\Http\Controllers\API\OrderController::class, 'store']);
    Route::get('/checkVoucher/{code}', [\App\Http\Controllers\API\VoucherController::class, 'checkVoucher']);
});
Route::post('/changePassWord', [UserController::class, 'changePassWord']);
