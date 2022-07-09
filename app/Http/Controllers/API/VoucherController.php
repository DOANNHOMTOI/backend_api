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
        $limit = $request->page = -1 ? 1000000 : env('APP_LIMIT_PAGE');
        return $this->sendResponse(Voucher::orderBy('created_at','DESC')->paginate($limit),'success');
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|min:6|max:6',
            'start_time' => 'required',
            'end_time' => 'required',
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
        $cluster->start_time = $request->start_time;
        $cluster->end_time = $request->end_time;
        $cluster->code = trim($request->code);
        $cluster->percent_value = (int)$request->percent_value;
        $cluster->is_active = 1;
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
    public function checkVoucher($code){
        return $this->sendResponse(Voucher::where('code',$code)->first(), 'success');
    }
    public function update(Request $request,$id){
        $cluster = Voucher::find($id);
        $cluster->title = trim($request->title);
        $cluster->start_time = $request->start_time;
        $cluster->end_time = $request->end_time;
        $cluster->percent_value = (int)$request->percent_value;
        $cluster->is_active = $request->is_active;
        $cluster->save();
        return $this->sendResponse($cluster, 'success');
    }
}
