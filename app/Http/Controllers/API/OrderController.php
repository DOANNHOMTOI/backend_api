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
        $list = Order::orderBy('created_at', 'DESC');
        if ($request->sku != null){
            $list = $list->where('sku','like','%'.strtolower($request->sku).'%');
        }
        if ($request->phone != null){
            $phones = Customer::where('phone','like','%'. $request->phone .'%')->pluck('id');
            $list = $list->whereIn('customer_id', $phones);
        }
        $list = $list->paginate(env('APP_LIMIT_PAGE'))->toArray();
        return $this->sendResponse($list, 'success');
    }

    public function store(Request $request)
    {
        $issetSKU = Product::where('sku', strtoupper($request->sku))->first();
        if ($issetSKU != null) {
            return $this->sendError('SKU đã tổn tại', 'error', 200);
        }
        $issetName = Product::where('name', $request->name)->first();
        if ($issetName != null) {
            return $this->sendError('Tên sản phẩm đã tổn tại', 'error', 200);
        }
        // Thanh toán chung
        $order = new Order();
        DB::beginTransaction();
        try {
            $customer = new Customer();
            $customer->name = trim($request->name);
            $customer->phone = $request->phone;
            $customer->email = $request->email;
            $customer->address = $request->address;
            $customer->note = $request->note_customer;
            $saveCustomer = $customer->save();

            if($saveCustomer) {
                $storeOrder = new Order();
                $storeOrder->sku = 'TJ' . time();
                $storeOrder->customer_id = $customer->id;
                $storeOrder->products = json_encode($request->products);
                $storeOrder->total_price = $request->total_price;
                $storeOrder->shipment_type = $request->shipment_type;
                $storeOrder->payment_type = $request->payment_type;
                // $storeOrder->date_storeOrder = time();
                $storeOrder->voucher_id = $request->voucher_id;
                $storeOrder->status = Order::CANCEL;
                $storeOrder->note = $request->note;
                $storeOrder->save();
                // foreach ($request->products as $k=>$value){
                //     $pr = Product::find($value['product']['id']);
                //     if(!$pr) {
                //         $pr->buyer = $pr->buyer + $value['qty'];
                //         $pr->save();
                //     }
                // }
                $order = $storeOrder;
            }
            DB::commit();
        } catch (\Exception $e) {

            DB::rollback();
            dd($e);
            return $this->sendError('Error !', $e->getMessage());
        }

            if($request->payment_type == Order::CASH_ON_DELIVERY) {
                $order->status = Order::NEW;
                $order->save();
                return $this->sendResponse($customer,true);
            }
            else if($request->payment_type == Order::QR_CODE) {
                $endpoint = Order::END_POINT;
                $partnerCode = Order::PARTNER_CODE;
                $accessKey = Order::ACCESS_KEY;
                $secretKey = Order::SECRET_KEY;
                $orderInfo = "Thanh toán qua MoMo";
                $amount = $order->total_price;
                $orderId = $order->sku;
                $redirectUrl = Order::CALLBACK_MOMO;
                $ipnUrl = Order::IPN_URL;
                $extraData = "";
                $requestId = time() . "";
                $requestType = "captureWallet";
                $rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;
                $signature = hash_hmac("sha256", $rawHash, $secretKey);
                $data = array('partnerCode' => $partnerCode,
                    'partnerName' => "Test",
                    "storeId" => "MomoTestStore",
                    'requestId' => $requestId,
                    'amount' => $amount,
                    'orderId' => $orderId,
                    'orderInfo' => $orderInfo,
                    'redirectUrl' => $redirectUrl,
                    'ipnUrl' => $ipnUrl,
                    'lang' => 'vi',
                    'extraData' => $extraData,
                    'requestType' => $requestType,
                    'signature' => $signature);
                $result = $this->execPostRequest($endpoint, json_encode($data));
                $jsonResult = json_decode($result, true);  // decode json
                // save add info order
                return $this->sendResponse($jsonResult,true);
            }
            else if($request->payment_type == Order::ATM) {
                $endpoint = Order::END_POINT;
                $partnerCode = Order::PARTNER_CODE;
                $accessKey = Order::ACCESS_KEY;
                $secretKey = Order::SECRET_KEY;
                $orderInfo = "Thanh toán qua MoMo";
                $orderId = $order->sku;
                $redirectUrl = Order::CALLBACK_MOMO;
                $ipnUrl = Order::IPN_URL;
                $amount = $order->total_price;
                $extraData = "";
                $requestId = time() . "";
                $requestType = "payWithATM";
                    //before sign HMAC SHA256 signature
                    $rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;
                    $signature = hash_hmac("sha256", $rawHash, $secretKey);
                    $data = array('partnerCode' => $partnerCode,
                        'partnerName' => "Test",
                        "storeId" => "MomoTestStore",
                        'requestId' => $requestId,
                        'amount' => $amount,
                        'orderId' => $orderId,
                        'orderInfo' => $orderInfo,
                        'redirectUrl' => $redirectUrl,
                        'ipnUrl' => $ipnUrl,
                        'lang' => 'vi',
                        'extraData' => $extraData,
                        'requestType' => $requestType,
                        'signature' => $signature);
                    $result = $this->execPostRequest($endpoint, json_encode($data));
                    $jsonResult = json_decode($result, true);  // decode json
                    return $this->sendResponse($jsonResult,true);
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
    public function callback(Request $request) {
        if($request->orderId) {
            $order = Order::where("sku", $request->orderId)->first();
            $order->status = Order::NEW;
            $order->save();
            $dataSuccess = ["type"=>"success", "message"=>"Thanh toán đơn hàng thành công"];
            return $this->sendResponse($dataSuccess, 'success');
        }
        return $this->sendError('Có lỗi xảy ra', 'error', 200);
    }
    public function execPostRequest($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
        );
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        //execute post
        $result = curl_exec($ch);
        //close connection
        curl_close($ch);
        return $result;
    }
}
