<?php

namespace App\Http\Controllers\API;

use App\Customer;
use App\Guest;
use App\Http\Controllers\Controller;
use App\Order;
use App\Product;
use App\ProductCategory;
use App\User;
use App\Voucher;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{

    public function order(Request $request)
    {

        $list = Order::orderBy('created_at', 'DESC');
        if ($request->start_time != null && $request->end_time != null){
            $start = new Carbon(date('Y-m-d H:i:s', $request->start_time));
            $end = new Carbon(date('Y-m-d H:i:s', $request->end_time));
            $list = $list->where('created_at', '>=', $start)
                        ->where('created_at', '<=', $end);
        }
        if ($request->status != null){
            $list = $list->where('status', (int)$request->status);
        }
        $list = $list->get()->toArray();

        $datas = [];
        $datas['total_order'] = count($list);
        $datas['NEW'] = 0;
        $datas['PROCESSING'] = 0;
        $datas['SUCCESS'] =0;
        $datas['CANCEL'] = 0;
        $datas['total_price'] = 0;
        foreach ($list as $k => $value) {
           if ($value['status'] == '1'){
               $datas['NEW'] ++;
           }
            if ($value['status'] == '2'){
                $datas['PROCESSING'] ++;
            }
            if ($value['status'] == '3'){
                $datas['SUCCESS'] ++;
            }
            if ($value['status'] == '4'){
                $datas['CANCEL'] ++;
            }
            $datas['total_price'] += $value['total_price'];
        }
        return $this->sendResponse($datas,'success');
    }
}
