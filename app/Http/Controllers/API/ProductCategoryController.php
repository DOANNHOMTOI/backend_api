<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductCategoryController extends Controller
{

    public function index(Request $request)
    {
        $limit = $request->page = -1 ? 1000000 : env('APP_LIMIT_PAGE');
        return $this->sendResponse(ProductCategory::orderBy('created_at','DESC')->paginate($limit),'success');
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $cluster = new ProductCategory();
        $cluster->name = trim($request->name);
        $cluster->save();

        return $this->sendResponse($cluster, 'success');
    }
    public function detail($id){
        return $this->sendResponse(ProductCategory::find($id), 'success');
    }
    public function delete($id){
        ProductCategory::find($id)->delete();
        return $this->sendResponse($id, 'success');
    }
    public function update(Request $request,$id){
        $cluster = ProductCategory::find($id);
        $cluster->name = trim($request->name);
        $cluster->is_active = $request->is_active;
        $cluster->save();
        return $this->sendResponse($cluster, 'success');
    }
}
