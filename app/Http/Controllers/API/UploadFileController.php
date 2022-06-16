<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\WPClusters;
use App\WPRegions;
use App\WPServers;
use App\WPSizes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UploadFileController extends Controller
{

    public function upload(Request $request){
        $file = $request->file('file');
        $fileName = time() . '_' .$file->getClientOriginalName();
        $date = date('Y') . '/' . date('m') . '/' . date('d');
        $destinationPath = public_path() . '/' . $date;
        $file->move($destinationPath, $fileName);

        return $this->sendResponse($date . '/' . $fileName, 'success');
    }
}
