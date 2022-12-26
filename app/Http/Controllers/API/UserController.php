<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\NotifyMail;
use App\Mail\PassWordMail;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Hash;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{

    public function __construct(User $user){
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function register(Request $request){
        $isset = User::where('email', $request->get('email'))->first();
        if ($isset){
            return $this->sendError('Tài khoản đã tổn tại !');
        }
        $user = User::create([
            'type' => $request->get('type'),
            'email' => $request->get('email'),
            'name' => $request->get('email'),
            'password' => bcrypt($request->get('password'))
        ]);

        return $this->sendResponse($user, 'success');
    }

    public function login(Request $request){
        $credentials = $request->only('email', 'password');
        $token = null;
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return $this->sendError('Thông tin đăng nhập không đúng !',400);
            }
        } catch (JWTAuthException $e) {
            return $this->sendError('Fail to gen token !',500);
        }
        $data = $this->createNewToken($token)->original;
        return $this->sendResponse($data, 'success');
    }

    public function getUserInfo(Request $request){
        return response()->json(auth()->user());
    }
    public function refresh() {
        return $this->createNewToken(auth()->refresh());
    }
    public function logout() {
        auth()->logout();

        return response()->json(['message' => 'User successfully signed out']);
    }
    protected function createNewToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
//            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }
    public function changePassWord(Request $request) {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        $userId = auth()->user()->id;

        $user = User::where('id', $userId)->update(
            ['password' => bcrypt($request->new_password)]
        );

        return response()->json([
            'message' => 'User successfully changed password',
            'user' => $user,
        ], 200);
    }
    public function validateToken(Request $request){
        return 1;
    }
}
