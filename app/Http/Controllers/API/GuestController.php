<?php

namespace App\Http\Controllers\API;

use App\Customer;
use App\Http\Controllers\Controller;
use App\ProductCategory;
use App\Guest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GuestController extends Controller
{
    public function index(Request $request){
        $limit = $request->page = -1 ? 1000000 : env('APP_LIMIT_PAGE');
        return $this->sendResponse(Guest::orderBy('created_at','DESC')->paginate($limit),'success');
    }
    public function store(Request $request)
    {
        $guest = Guest::where('phone',$request->phone)->first();
        if ($guest != null){
            return $this->sendError('Số điện thoại đã đăng ký tài khoản khác','Số điện thoại đã đăng ký tài khoản khác');
        }
        $guest = new Guest();
        $guest->name = trim($request->name);
        $guest->phone = $request->phone;
        $guest->email = $request->email;
        $guest->address = $request->address;
        $guest->password = $request->password;
        $guest->status = 1;
        $guest->save();

        return $this->sendResponse($guest, 'success');
    }
    public function login(Request $request)
    {
        $guest = Guest::where('phone',$request->phone)->where('password',$request->password)->where('status',1)->first();
        return $this->sendResponse($guest, 'success');
    }
}
