<?php
namespace TsaiYiHua\ECPay;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class QueryTradeInfo
{
    use ECPayTrait;

    protected $apiUrl;
    protected $postData;
    protected $merchantId;
    protected $hashKey;
    protected $hashIv;
    protected $encryptType='sha256';

    public function __construct()
    {
        if (config('app.env') == 'production') {
            $this->apiUrl = 'https://payment.ecpay.com.tw/Cashier/QueryTradeInfo/V5';
        } else {
            $this->apiUrl = 'https://payment-stage.ecpay.com.tw/Cashier/QueryTradeInfo/V5';
        }
        $this->postData = new Collection();

        $this->merchantId = config('ecpay.MerchantId');
        $this->hashKey = config('ecpay.HashKey');
        $this->hashIv = config('ecpay.HashIV');
    }

    public function getData($orderId)
    {
        $this->postData->put('MerchantID', $this->merchantId);
        $this->postData->put('MerchantTradeNo', $orderId);
        $this->postData->put('TimeStamp', Carbon::now()->timestamp);
        return $this;
    }
}