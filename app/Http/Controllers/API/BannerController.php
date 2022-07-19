<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\ProductCategory;
use App\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BannerController extends Controller
{

    public function index(Request $request)
    {
        $limit = $request->page = -1 ? 1000000 : env('APP_LIMIT_PAGE');
        return $this->sendResponse(Banner::orderBy('created_at','DESC')->paginate($limit),'success');
    }
    public function store(Request $request)
    {
        $banner = new Banner();
        $banner->title = trim($request->title);
        $banner->image = $request->image;
        $banner->is_active = 1;
        $banner->save();

        return $this->sendResponse($banner, 'success');
    }
    public function detail($id){
        return $this->sendResponse(Banner::find($id), 'success');
    }
    public function delete($id){
        Banner::find($id)->delete();
        return $this->sendResponse($id, 'success');
    }
    public function update(Request $request,$id){
        $banner = Banner::find($id);
        $banner->title = trim($request->title);
        $banner->image = $request->image;
        $banner->is_active = $request->is_active;
        $banner->save();
        return $this->sendResponse($banner, 'success');
    }
}
