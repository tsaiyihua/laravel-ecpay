<?php
/**
 * Created by PhpStorm.
 * User: yihua
 * Date: 2018/11/27
 * Time: 下午 3:30
 */

namespace TsaiYiHua\ECPay;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use TsaiYiHua\ECPay\Collections\InvoicePostCollection;
use TsaiYiHua\ECPay\Exceptions\ECPayException;
use TsaiYiHua\ECPay\Libs\ECPayInvoice;
use TsaiYiHua\ECPay\Services\StringService;
use TsaiYiHua\ECPay\Validations\InvoiceValidation;

class Invoice
{
    use ECPayTrait;

    protected $apiUrl;
    protected $postData;
    protected $merchantId;
    protected $hashKey;
    protected $hashIv;
    protected $encryptType='md5';

    protected $checkMacValueIgnoreFields;

    public $ecpayInvoice;

    public function __construct(InvoicePostCollection $postData, ECPayInvoice $ecpayInvoice)
    {
        if (config('app.env') == 'production') {
            $this->apiUrl = 'https://einvoice.ecpay.com.tw/Invoice/Issue';
        } else {
            $this->apiUrl = 'https://einvoice-stage.ecpay.com.tw/Invoice/Issue';
        }
        $this->postData = $postData;

        $this->ecpayInvoice = $ecpayInvoice;
        $this->ecpayInvoice->Invoice_Method = 'INVOICE' ;
        $this->ecpayInvoice->Invoice_Url = $this->apiUrl;
        $this->ecpayInvoice->MerchantID = config('ecpay.MerchantId');
        $this->ecpayInvoice->HashKey = config('ecpay.InvoiceHashKey');
        $this->ecpayInvoice->HashIV = config('ecpay.InvoiceHashIV');
    }

    /**
     * @param $invData
     * @return $this
     * @throws Exceptions\ECPayException
     */
    public function setPostData($invData)
    {
        $this->postData->setData($invData)->setPostRawData();
        foreach($this->postData->all() as $key=>$val) {
            $this->ecpayInvoice->Send[$key] = $val;
        }
        return $this;
    }
}
