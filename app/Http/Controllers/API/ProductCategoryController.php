<?php

namespace App\Http\Controllers\API;

use App\Product;
use App\Http\Controllers\Controller;
use App\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductCategoryController extends Controller
{

    public function index(Request $request)
    {
        if ($request->page == -1) {
            $limit =   1000000;
        } else {
            $limit = env('APP_LIMIT_PAGE');
        }
        return $this->sendResponse(ProductCategory::orderBy('position', 'ASC')->paginate($limit), 'success');
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $issetName = ProductCategory::where('name', $request->name)->first();
        if ($issetName != null) {
            return $this->sendError('Tên loại sản phẩm đã tổn tại', 'error', 200);
        }

        $cluster = new ProductCategory();
        $cluster->name = trim($request->name);
        $cluster->position = $request->position;
        $cluster->save();

        return $this->sendResponse($cluster, 'success');
    }
    public function detail($id)
    {
        return $this->sendResponse(ProductCategory::find($id), 'success');
    }
    public function delete($id)
    {
        ProductCategory::find($id)->delete();
        Product::where('category_id', $id)->delete();
        return $this->sendResponse($id, 'success');
    }
    public function update(Request $request, $id)
    {
        $cluster = ProductCategory::find($id);
        $cluster->name = trim($request->name);
        $cluster->is_active = $request->is_active;
        $cluster->position = $request->position;
        $cluster->save();
        return $this->sendResponse($cluster, 'success');
    }
}
