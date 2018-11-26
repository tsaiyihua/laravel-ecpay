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

    public function __construct()
    {
        if (config('app.env') == 'production') {
            $this->apiUrl = 'https://payment.ecpay.com.tw/Cashier/QueryTradeInfo/V5';
        } else {
            $this->apiUrl = 'https://payment-stage.ecpay.com.tw/Cashier/QueryTradeInfo/V5';
        }
        $this->postData = new Collection();

        $this->merchantId = config('ecpay.MerchantId');
    }

    public function getData($data)
    {
        $this->postData->put('MerchantID', $this->merchantId);
        $this->postData->put('MerchantTradeNo', $data['orderId']);
        $this->postData->put('TimeStamp', Carbon::now()->timestamp);
        return $this;
    }
}