<?php

namespace App\Http\Controllers\API;

use App\Customer;
use App\Http\Controllers\Controller;
use App\Mail\PassWordMail;
use App\ProductCategory;
use App\Guest;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class GuestController extends Controller
{
    public function index(Request $request){
        $limit = $request->page = -1 ? 1000000 : env('APP_LIMIT_PAGE');
        return $this->sendResponse(Guest::orderBy('created_at','DESC')->paginate($limit),'success');
    }
    public function store(Request $request)
    {
        $guestPhone = Guest::where('phone',$request->phone)->first();
        $guestEmail = Guest::where('email',$request->email)->first();

        if ($guestPhone != null ||$guestEmail !=null ){
            return $this->sendError('Số điện thoại hoặc email đã đăng ký tài khoản khác','Số điện thoại đã đăng ký tài khoản khác');
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

    public function forgetPass(Request $request){

        $validator = Validator::make($request->all(), [
            'email' => 'required',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $newPass = rand(100000, 999999);
        $guest = Guest::where('email', $request->email)->first();
        if (empty($guest)){
            return $this->sendError(null,'Không tìm thấy tài khoản nào');
        }
        $guest->update(
            ['password' => $newPass]
        );

        // gửi mail mật khẩu mới
        Mail::to($request->email)->send(new PassWordMail($newPass));

        return response()->json([
            'message' => 'Mật khẩu mới đã gửi về email của bạn',
        ], 200);
    }
}
