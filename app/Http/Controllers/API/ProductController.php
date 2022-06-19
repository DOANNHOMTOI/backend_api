<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Product;
use App\ProductColor;
use App\ProductImage;
use App\ProductSize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{

    public function index(Request $request)
    {
        return $this->sendResponse(Product::paginate(env('APP_LIMIT_PAGE')), 'success');
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'qty' => 'required',
            'sku' => 'required',
            'price' => 'required',
            'excerpt' => 'required',
            'description' => 'required',
            'image' => 'required',
            'category_id' => 'required',
            'sizes' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $issetSKU = Product::where('sku', strtoupper($request->sku))->first();
        if ($issetSKU != null) {
            return $this->sendError('SKU đã tổn tại', 'error');
        }
        $issetName = Product::where('name', $request->name)->first();
        if ($issetName != null) {
            return $this->sendError('Tên sản phẩm đã tổn tại', 'error');
        }

        DB::beginTransaction();
        try {
            $product = new Product();
            $product->name = trim($request->name);
            $product->qty = $request->qty;
            $product->sku = strtoupper($request->sku);
            $product->price = $request->price;
            $product->price_sale = $request->price_sale;
            $product->excerpt = $request->excerpt;
            $product->description = $request->description;
            $product->body = $request->body;
            $product->image = $request->image;
            $product->category_id = $request->category_id;
            $product->is_active = 1;
            $saveProduct = $product->save();
            if ($saveProduct) {
                $listColor = explode(",", $request->colors);
                foreach ($listColor as $k => $value) {
                    $color = new ProductColor();
                    $color->product_id = $product->id;
                    $color->color = $value;
                    $color->save();
                }
                // SIZE
                $listSize = explode(",", $request->sizes);
                foreach ($listSize as $k => $value) {
                    $color = new ProductSize();
                    $color->product_id = $product->id;
                    $color->size = $value;
                    $color->save();
                }
                // IMAGE
                $listImage = explode(",", $request->images);
                foreach ($listImage as $k => $value) {
                    $color = new ProductImage();
                    $color->product_id = $product->id;
                    $color->image = $value;
                    $color->save();
                }
            }
            DB::commit();
            return $this->sendResponse($product, 'success');
            // all good
        } catch (\Exception $e) {
            DB::rollback();
            // something went wrong
            return $this->sendError('Error !', 'error');
        }
    }

    public function detail($id)
    {
        $object = new \stdClass();
        $object->infor = Product::find($id);
        $object->colors = ProductColor::where('product_id',$id)->get();
        $object->images = ProductImage::where('product_id',$id)->get();
        $object->sizes = ProductSize::where('product_id',$id)->get();
        return $this->sendResponse($object, 'success');
    }

    public function delete($id)
    {
        Product::find($id)->delete();
        ProductColor::where('product_id',$id)->delete();
        ProductSize::where('product_id',$id)->delete();
        ProductImage::where('product_id',$id)->delete();
        return $this->sendResponse($id, 'success');
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'qty' => 'required',
            'sku' => 'required',
            'price' => 'required',
            'excerpt' => 'required',
            'description' => 'required',
            'image' => 'required',
            'category_id' => 'required',
            'sizes' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
//        $issetSKU = Product::where('sku', strtoupper($request->sku))->first();
//        if ($issetSKU != null) {
//            return $this->sendError('SKU đã tổn tại', 'error');
//        }
//        $issetName = Product::where('name', $request->name)->first();
//        if ($issetName != null) {
//            return $this->sendError('Tên sản phẩm đã tổn tại', 'error');
//        }

        DB::beginTransaction();
        try {
            $product = Product::find($id);
            $product->name = trim($request->name);
            $product->qty = $request->qty;
            $product->sku = strtoupper($request->sku);
            $product->price = $request->price;
            $product->price_sale = $request->price_sale;
            $product->excerpt = $request->excerpt;
            $product->description = $request->description;
            $product->body = $request->body;
            $product->image = $request->image;
            $product->category_id = $request->category_id;
            $product->is_active = $request->is_active;
            $saveProduct = $product->save();
            if ($saveProduct) {
                // xóa hết data cũ
                ProductColor::where('product_id',$id)->delete();

                $listColor = explode(",", $request->colors);
                foreach ($listColor as $k => $value) {
                    $color = new ProductColor();
                    $color->product_id = $product->id;
                    $color->color = $value;
                    $color->save();
                }
                // SIZE
                ProductSize::where('product_id',$id)->delete();
                $listSize = explode(",", $request->sizes);
                foreach ($listSize as $k => $value) {
                    $color = new ProductSize();
                    $color->product_id = $product->id;
                    $color->size = $value;
                    $color->save();
                }
                // IMAGE
                ProductImage::where('product_id',$id)->delete();
                $listImage = explode(",", $request->images);
                foreach ($listImage as $k => $value) {
                    $color = new ProductImage();
                    $color->product_id = $product->id;
                    $color->image = $value;
                    $color->save();
                }
            }
            DB::commit();
            return $this->sendResponse($product, 'success');
            // all good
        } catch (\Exception $e) {
            DB::rollback();
            // something went wrong
            return $this->sendError('Error !', 'error');
        }
    }
}
