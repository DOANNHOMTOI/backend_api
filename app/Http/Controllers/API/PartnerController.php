<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class PartnerController extends Controller
{

    public function index(Request $request)
    {
        return $this->sendResponse(User::paginate(env('APP_LIMIT_PAGE')),'success');
    }
    public function store(Request $request)
    {
        if (User::where('email',$request->email)->first()){
            return $this->sendError('Email này đã tạo tài khoản trước đó', 'Email này đã tạo tài khoản trước đó');
        }
        $User = new User();
        $User->email = trim($request->email);
        $User->password =  bcrypt($request->get('password'));
        $User->save();

        return $this->sendResponse($User, 'success');
    }
    public function detail($id){
        return $this->sendResponse(User::find($id), 'success');
    }
    public function delete($id){
        User::find($id)->delete();
        return $this->sendResponse($id, 'success');
    }
    public function update(Request $request,$id){
        $User = User::find($id);
        $User->name = trim($request->title);
        $User->save();
        return $this->sendResponse($User, 'success');
    }
}
