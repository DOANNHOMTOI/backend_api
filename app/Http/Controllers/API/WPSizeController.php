<?php

namespace App\Http\Controllers\API;

use App\Clusters;
use App\Http\Controllers\Controller;
use App\WPRegions;
use App\WPSizes;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class WPSizeController extends Controller
{

    public function index(Request $request)
    {
        return $this->sendResponse(WPSizes::all(),'success');
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'region_id' => 'required',
            'cpu' => 'required',
            'ram' => 'required',
            'price' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $cluster = new WPSizes();
        $cluster->name = trim($request->name);
        $cluster->region_id = $request->region_id;
        $cluster->cpu = $request->cpu;
        $cluster->ram = $request->ram;
        $cluster->price = $request->price;
        $cluster->sale_price = $request->sale_price;
        $cluster->status = 1;
        $cluster->save();

        return $this->sendResponse($cluster, 'success');
    }
    public function detail($id){
        return $this->sendResponse(WPSizes::find($id), 'success');
    }
    public function delete($id){
        WPSizes::find($id)->delete();
        return $this->sendResponse($id, 'success');
    }
}
