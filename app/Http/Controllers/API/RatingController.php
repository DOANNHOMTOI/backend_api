<?php

namespace App\Http\Controllers\API;

use App\Guest;
use App\Http\Controllers\Controller;
use App\Product;
use App\ProductCategory;
use App\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RatingController extends Controller
{

    public function index(Request $request)
    {
        $limit = $request->page = -1 ? 1000000 : env('APP_LIMIT_PAGE');
        return $this->sendResponse(Rating::orderBy('created_at','DESC')->paginate($limit),'success');
    }
    public function store(Request $request)
    {
        $cluster = new Rating();
        $cluster->product_id = $request->product_id;
        $cluster->product_name = Product::find($request->product_id)->name;
        $cluster->guest_id = Guest::find($request->guest_id)->name;
        $cluster->rate = $request->rate;
        $cluster->comment = $request->comment;
        $cluster->status = 0;
        $cluster->save();

        return $this->sendResponse($cluster, 'success');
    }
    public function detail($id){
        return $this->sendResponse(Rating::find($id), 'success');
    }
    public function delete($id){
        Rating::find($id)->delete();
        return $this->sendResponse($id, 'success');
    }
    public function checkRating($code){
        return $this->sendResponse(Rating::where('code',$code)->first(), 'success');
    }
    public function update(Request $request,$id){
        $rate = Rating::find($id);
        $rate->status = 1;
        $rate->save();
        return $this->sendResponse($rate, 'success');
    }
    public function getByProduct(Request $request){
        $arr = [];
        $list = Rating::where('product_id',$request->product)->where('status',1)->get();
        foreach ($list as $k=>$v){
            $v->guest_info = Guest::find($v->guest_id);
            array_push($arr,$v);
        }
        $isComment = true;
        if ($request->user != null){
            $issetRate = Rating::where('product_id',$request->product)->where('guest_id',$request->user)->first();
            if ($issetRate){
                $isComment = false;
            }
        }
        $data['list'] = $arr;
        $data['is_comment'] = $isComment;
        return $this->sendResponse($data, 'success');
    }
}
