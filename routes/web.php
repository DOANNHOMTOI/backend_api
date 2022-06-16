<?php

use App\Http\Controllers\API\ClusterController;
use App\Http\Controllers\API\RegionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/test', function () {
    return view('welcome');
});
//Route::get('api/v1/clusters', [ClusterController::class, 'index']);
//Route::get('api/v1/clusters/{id}', [ClusterController::class, 'detail']);
//Route::post('api/v1/clusters', [ClusterController::class, 'store']);
//
//Route::get('api/v1/regions', [RegionController::class, 'index']);
//Route::get('api/v1/regions/{id}', [RegionController::class, 'detail']);
//Route::post('api/v1/regions', [RegionController::class, 'store']);

