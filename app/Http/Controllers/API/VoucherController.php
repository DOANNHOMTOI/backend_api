<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\ProductCategory;
use App\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VoucherController extends Controller
{

    public function index(Request $request)
    {
        return $this->sendResponse(Voucher::all(),'success');
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required',
            'title' => 'required',
            'percent_value' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
        if ( Voucher::where('code',$request->code)->first() != null){
            return $this->sendError('Code đã tổn tại !', 'error');
        }
        $cluster = new Voucher();
        $cluster->title = trim($request->title);
        $cluster->code = trim($request->code);
        $cluster->percent_value = (int)$request->percent_value;
        $cluster->save();

        return $this->sendResponse($cluster, 'success');
    }
    public function detail($id){
        return $this->sendResponse(Voucher::find($id), 'success');
    }
    public function delete($id){
        Voucher::find($id)->delete();
        return $this->sendResponse($id, 'success');
    }
    public function update(Request $request,$id){
        $cluster = Voucher::find($id);
        $cluster->title = trim($request->title);
        $cluster->percent_value = (int)$request->percent_value;
        $cluster->is_active = $request->is_active;
        $cluster->save();
        return $this->sendResponse($cluster, 'success');
    }
}
