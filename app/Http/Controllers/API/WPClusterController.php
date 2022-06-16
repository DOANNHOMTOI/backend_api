<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\WPClusters;
use App\WPRegions;
use App\WPServers;
use App\WPSizes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WPClusterController extends Controller
{

    public function index(Request $request)
    {
        $list = WPClusters::all();
        if ($request->region_id != null){
            $list = WPClusters::where('region_id',$request->region_id)->get();
        }
        $arrData = [];
        foreach ($list as $key=>$value){
            $value->region = WPRegions::find($value->region_id);
            $value->server = WPServers::find($value->region_id);
            $value->size = WPSizes::find($value->size_id);
            unset($value->region_id);
            unset($value->server_id);
            unset($value->size_id);
            array_push($arrData,$value);
        }
        return $this->sendResponse($arrData, 'success');
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'region_id' => 'required',
            'server_id' => 'required',
            'size_id' => 'required',
            'disk' => 'required',
            'file' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
        if(WPRegions::find($request->region_id) == null){
            return $this->sendError('region_id is invalid !');
        }
        if(WPServers::find($request->server_id) == null){
            return $this->sendError('server_id is invalid !');
        }
        $cluster = new WPClusters();
        $cluster->name = trim($request->name);
        $cluster->region_id = (int)($request->region_id);
        $cluster->server_id = (int)($request->server_id);
        $cluster->size_id = (int)($request->size_id);
        $cluster->disk = (int)($request->disk);
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
        $object = WPClusters::find($id);
        $object->size = WPSizes::find($object->size_id);
        $object->server = WPServers::find($object->server_id);
        $object->region = WPRegions::find($object->region_id);
        unset($object->size_id);
        unset($object->server_id);
        unset($object->region_id);
        return $this->sendResponse($object, 'success');
    }
    public function delete($id){
        WPClusters::find($id)->delete();
        return $this->sendResponse($id, 'success');
    }
}
