<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Product;
use App\ProductCategory;
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
        $list = Product::orderBy('created_at', 'DESC')->paginate(env('APP_LIMIT_PAGE'))->toArray();
        foreach ($list['data'] as $key => $value) {
            $value['category'] = ProductCategory::where('id', $value['category_id'])->first();
            $list['data'][$key] = $value;
        }
        return $this->sendResponse($list, 'success');
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'qty' => 'required',
            'sku' => 'required',
            'price' => 'required',
            'image' => 'required',
            'category_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $issetSKU = Product::where('sku', strtoupper($request->sku))->first();
        if ($issetSKU != null) {
            return $this->sendError('SKU đã tổn tại', 'error', 200);
        }
        $issetName = Product::where('name', $request->name)->first();
        if ($issetName != null) {
            return $this->sendError('Tên sản phẩm đã tổn tại', 'error', 200);
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
        $object->colors = ProductColor::where('product_id', $id)->get();
        $object->images = ProductImage::where('product_id', $id)->get();
        $object->sizes = ProductSize::where('product_id', $id)->get();
        return $this->sendResponse($object, 'success');
    }

    public function delete($id)
    {
        Product::find($id)->delete();
        ProductColor::where('product_id', $id)->delete();
        ProductSize::where('product_id', $id)->delete();
        ProductImage::where('product_id', $id)->delete();
        return $this->sendResponse($id, 'success');
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'qty' => 'required',
            'sku' => 'required',
            'price' => 'required',
            'category_id' => 'required',
            'sizes' => 'required',
            'colors' => 'required',
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
            if ($request->image != null || $request->image != '') {
                $product->image = $request->image;
            }
            $product->category_id = $request->category_id;
            $product->is_active = $request->is_active;
            $saveProduct = $product->save();
            if ($saveProduct) {
                // xóa hết data cũ
                ProductColor::where('product_id', $id)->delete();

                $listColor = explode(",", $request->colors);
                foreach ($listColor as $k => $value) {
                    $color = new ProductColor();
                    $color->product_id = $product->id;
                    $color->color = $value;
                    $color->save();
                }
                // SIZE
                ProductSize::where('product_id', $id)->delete();
                $listSize = explode(",", $request->sizes);
                foreach ($listSize as $k => $value) {
                    $color = new ProductSize();
                    $color->product_id = $product->id;
                    $color->size = $value;
                    $color->save();
                }
                // IMAGE
                if ($request->images != null || $request->images != '') {
                    ProductImage::where('product_id', $id)->delete();
                    $listImage = explode(",", $request->images);
                    foreach ($listImage as $k => $value) {
                        $color = new ProductImage();
                        $color->product_id = $product->id;
                        $color->image = $value;
                        $color->save();
                    }
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

    public function productFilter(Request $request)
    {
        $listProduct = Product::orderBy('created_at', 'DESC');
        if ($request->get('name') != null) {
            $listProduct->where('name', 'like','%'.$request->get('name').'%')
                        ->orWhere('excerpt', 'like','%'.$request->get('name').'%')
                        ->orWhere('description', 'like','%'.$request->get('name').'%');
        }
        if ($request->get('category_id') != null) {
            $listProduct->where('category_id', $request->category_id);
        }
        if ($request->minPrice != null && $request->maxPrice != null) {
            $listProduct->whereBetween('price', [(int)$request->minPrice,(int)$request->maxPrice]);
        }
        $products = $listProduct->get();
        $categories = [];
        foreach (ProductCategory::all() as $value) {
            $value['numProduct'] = Product::where('category_id', $value->id)->count();
            array_push($categories, $value);
        }
        $listNew = Product::orderBy('created_at', 'DESC')->take(5)->get();
        $data = [
            'products' => $products,
            'categories' => $categories,
            'product_new' => $listNew,
        ];

        return $this->sendResponse($data, 'success');
    }
    public function productDetail($id){
        $product = Product::find($id);
        $listImage = ProductImage::where('product_id',$id)->get();
        $listColor = ProductColor::where('product_id',$id)->get();
        $listSize = ProductSize::where('product_id',$id)->get();
        $listOther = Product::where('category_id',$product->category_id)->where('id','!=',$id)->get();
        $data = [
            'detail' => $product,
            'images' => $listImage,
            'colors' => $listColor,
            'sizes' => $listSize,
            'other' => $listOther,
        ];

        return $this->sendResponse($data, 'success');
    }
    public function productNews(){
        $data = Product::orderBy('created_at','DESC')->limit(8)->get();
        return $this->sendResponse($data, 'success');
    }
    public function productCare(Request $request){
        $data = Product::whereIn('category_id',explode(",",$request->categories))->limit(8)->get();
        return $this->sendResponse($data, 'success');
    }
}
