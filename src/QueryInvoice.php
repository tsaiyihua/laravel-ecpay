<?php
/**
 * Created by PhpStorm.
 * User: yihua
 * Date: 2018/11/26
 * Time: 下午 6:10
 */

namespace TsaiYiHua\ECPay;


use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;

class QueryInvoice
{
    use ECPayTrait;

    protected $apiUrl;
    protected $postData;
    protected $merchantId;
    protected $hashKey;
    protected $hashIv;
    protected $encryptType='md5';

    protected $client;

    public function __construct(Client $client)
    {
        if (config('app.env') == 'production') {
            $this->apiUrl = 'https://einvoice.ecpay.com.tw/Query/Issue';
        } else {
            $this->apiUrl = 'https://einvoice-stage.ecpay.com.tw/Query/Issue';
        }
        $this->postData = new Collection();

        $this->merchantId = config('ecpay.MerchantId');
        $this->hashKey = config('ecpay.InvoiceHashKey');
        $this->hashIv = config('ecpay.InvoiceHashIV');

        $this->client = $client;
    }

    public function getData($data)
    {
        $this->postData->put('TimeStamp', Carbon::now()->timestamp);
        $this->postData->put('MerchantID', $this->merchantId);
        $this->postData->put('RelateNumber', $data['orderId']);
        /** @todo use guzzle to get info from ecpay */
        return $this;
    }
}