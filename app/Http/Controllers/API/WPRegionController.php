<?php

namespace App\Http\Controllers\API;

use App\Clusters;
use App\Http\Controllers\Controller;
use App\WPRegions;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class WPRegionController extends Controller
{

    public function index(Request $request)
    {
        return $this->sendResponse(WPRegions::all(),'success');
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $cluster = new WPRegions();
        $cluster->name = trim($request->name);
        $cluster->save();

        return $this->sendResponse($cluster, 'success');
    }
    public function detail($id){
        return $this->sendResponse(WPRegions::find($id), 'success');
    }
    public function delete($id){
        WPRegions::find($id)->delete();
        return $this->sendResponse($id, 'success');
    }
}
