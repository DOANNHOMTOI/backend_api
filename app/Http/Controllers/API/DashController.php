<?php

namespace App\Http\Controllers\API;

use App\Guest;
use App\Http\Controllers\Controller;
use App\Order;
use App\Product;
use App\ProductCategory;
use App\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DashController extends Controller
{

    public function index(Request $request)
    {
        $datas['total']['guests'] = Guest::count();
        $datas['total']['products'] = Product::count();
        $datas['total']['orders'] = Order::count();
        $datas['total']['categories'] = ProductCategory::count();


        $datas['recent']['order'] = Order::orderBy('created_at','DESC')->take(5)->get();
        $datas['recent']['product'] = Order::orderBy('created_at','DESC')->take(5)->get();
        return $this->sendResponse($datas,'success');
    }
}
