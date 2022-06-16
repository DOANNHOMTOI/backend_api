<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\WPServers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WPServerController extends Controller
{

    public function index(Request $request)
    {
        return $this->sendResponse(WPServers::all(),'success');
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $cluster = new WPServers();
        $cluster->name = trim($request->name);
        $cluster->save();

        return $this->sendResponse($cluster, 'success');
    }
    public function detail($id){
        return $this->sendResponse(WPServers::find($id), 'success');
    }
    public function delete($id){
        WPServers::find($id)->delete();
        return $this->sendResponse($id, 'success');
    }
}
