<?php


namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';
    public $timestamps = true;

    CONST CASH_ON_DELIVERY = 1;
    CONST QR_CODE = 2;
    CONST ATM = 3;

    // Thông tin cổng thanh toán
    const PARTNER_CODE = "MOMOBKUN20180529";
    const ACCESS_KEY = 'klm05TvNBzhg7h7j';
    const SECRET_KEY = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';


    // Trạng thái đơn hàng
    const NEW = 1;
    const PROCESSING = 2;
    const SUCCESS = 3;
    const CANCEL = 4;

    //MOMO
    const END_POINT = "https://test-payment.momo.vn/v2/gateway/api/create";
    const CALLBACK_MOMO = "http://localhost:8080/callback-momo";
    const IPN_URL = "http://localhost:8080/callback-momo";

}
