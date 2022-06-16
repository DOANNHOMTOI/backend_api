<?php

namespace App\Http\Controllers\API;

use App\Clusters;
use App\Http\Controllers\Controller;
use App\Regions;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ClusterController extends Controller
{

    public function index(Request $request)
    {
        $list = Clusters::all();
        if ($request->region_id != null){
            $list = Clusters::where('region_id',$request->region_id)->get();
        }
        $arrData = [];
        foreach ($list as $key=>$value){
            $value->region = Regions::find($value->region_id);
            unset($value->region_id);
            array_push($arrData,$value);
        }
        return $this->sendResponse($arrData, 'success');
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'region_id' => 'required',
            'file' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
        if(Regions::find($request->region_id) == null){
            return $this->sendError('Region is invalid !');
        }
        $cluster = new Clusters();
        $cluster->name = trim($request->name);
        $cluster->region_id = (int)($request->region_id);
        $cluster->status = 1;
        $cluster->file = $this->saveImgBase64($request->file('file'));
        $cluster->save();

        return $this->sendResponse($cluster, 'success');
    }
    protected function saveImgBase64($file)
    {
        $fileName = time() . '_' .$file->getClientOriginalName();
        $date = date('Y') . '/' . date('m') . '/' . date('d');
        $destinationPath = public_path() . '/' . $date;
        $file->move($destinationPath, $fileName);
        return base64_encode($date . '/' . $fileName);
    }
    public function detail($id){
        return $this->sendResponse(Clusters::find($id), 'success');
    }
    public function delete($id){
        Clusters::find($id)->delete();
        return $this->sendResponse($id, 'success');
    }
}
