<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\ProductCategory;
use App\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{

    public function index(Request $request)
    {
        $limit = $request->page = -1 ? 1000000 : env('APP_LIMIT_PAGE');
        return $this->sendResponse(Customer::orderBy('created_at','DESC')->paginate($limit),'success');
    }
    public function store(Request $request)
    {
        $customer = Customer::where('phone',$request->phone)->where('is_register',1)->first();
        if ($customer != null){
            return $this->sendError('Số điện thoại đã đăng ký tài khoản khác','Số điện thoại đã đăng ký tài khoản khác');
        }
        $customer = new Customer();
        $customer->name = trim($request->name);
        $customer->phone = $request->phone;
        $customer->email = $request->email;
        $customer->address = $request->address;
        $customer->password = $request->password;
        $saveCustomer = $customer->save();

        return $this->sendResponse($customer, 'success');
    }
    public function login(Request $request)
    {
        $customer = Customer::where('phone',$request->phone)->where('password',$request->password)->where('status',1)->where('is_register',1)->first();
//        if ($customer != null){
//            return $this->sendError('Thông tin đăng nhập không đúng !','Thông tin đăng nhập không đúng !');
//        }
        return $this->sendResponse($customer, 'success');
    }
    public function detail($id){
        return $this->sendResponse(Customer::find($id), 'success');
    }
    public function delete($id){
        Customer::find($id)->delete();
        return $this->sendResponse($id, 'success');
    }
    public function checkCustomer($code){
        return $this->sendResponse(Customer::where('code',$code)->first(), 'success');
    }
    public function update(Request $request,$id){
        $cluster = Customer::find($id);
        $cluster->title = trim($request->title);
        $cluster->start_time = $request->start_time;
        $cluster->end_time = $request->end_time;
        $cluster->percent_value = (int)$request->percent_value;
        $cluster->is_active = $request->is_active;
        $cluster->save();
        return $this->sendResponse($cluster, 'success');
    }
}
