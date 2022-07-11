<?php

namespace App\Http\Controllers\API;

use App\Customer;
use App\Http\Controllers\Controller;
use App\Order;
use App\Permission;
use App\Product;
use App\ProductCategory;
use App\ProductColor;
use App\ProductImage;
use App\ProductSize;
use App\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{

    public function index(Request $request)
    {
        $list = Order::orderBy('created_at', 'DESC')->paginate(env('APP_LIMIT_PAGE'))->toArray();
        return $this->sendResponse($list, 'success');
    }

    public function store(Request $request)
    {
//
//        $validator = Validator::make($request->all(), [
//            'name' => 'required',
//            'qty' => 'required',
//            'sku' => 'required',
//            'price' => 'required',
//            'image' => 'required',
//            'category_id' => 'required',
//        ]);
//
//        if ($validator->fails()) {
//            return $this->sendError('Validation Error.', $validator->errors());
//        }
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
            $customer = new Customer();
            $customer->name = trim($request->name);
            $customer->phone = $request->phone;
            $customer->email = $request->email;
            $customer->address = $request->address;
            $customer->note = $request->note_customer;
            $saveCustomer = $customer->save();
            if ($saveCustomer) {
                $order = new Order();
                $order->sku = 'TJ' . time();
                $order->customer_id = $customer->id;
                $order->products = $request->products;
                $order->total_price = $request->total_price;
                $order->shipment_type = $request->shipment_type;
                $order->payment_type = $request->payment_type;
                $order->date_order = time();
                $order->status = 1;
                $order->voucher_id = $request->voucher_id;
                $order->note = $request->note;
                $order->save();
            }
            DB::commit();
            return $this->sendResponse($customer, 'success');
            // all good
        } catch (\Exception $e) {
            DB::rollback();
            // something went wrong
            return $this->sendError('Error !', $e->getMessage());
        }
    }

    public function detail($id){
        $order = Order::find($id);
        $order->customer = Customer::find($order->customer_id);
        $order->products = \GuzzleHttp\json_decode($order->products);
        $order->voucher_id = Voucher::find($order->voucher_id);
        return $this->sendResponse($order, 'success');
    }

    public function delete($id)
    {
        Product::find($id)->delete();
        ProductColor::where('product_id', $id)->delete();
        ProductSize::where('product_id', $id)->delete();
        ProductImage::where('product_id', $id)->delete();
        return $this->sendResponse($id, 'success');
    }

    public function update(Request $request,$id){
        $order = Order::find($id);
        $order->status = $request->status;
        $order->save();
        return $this->sendResponse($order, 'success');
    }

    public function productFilter(Request $request)
    {
        $listProduct = Product::orderBy('created_at', 'DESC');
        if ($request->get('category_id') != null) {
            $listProduct->where('category_id', (int)$request->get('category_id'));
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
}
