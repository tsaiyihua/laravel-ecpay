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

    public function __construct(InvoicePostCollection $postData)
    {
        if (config('app.env') == 'production') {
            $this->apiUrl = 'https://einvoice.ecpay.com.tw/Invoice/Issue';
        } else {
            $this->apiUrl = 'https://einvoice-stage.ecpay.com.tw/Invoice/Issue';
        }
        $this->postData = $postData;

        /**
         * 官方函式庫是 php 5.3 的寫法, 但因為在 php 7.4 導入 namespace 的環境下,
         * Ecpay_Invoice.php 也須需加入 namespace 的設定
         */
        require_once('Libs/Ecpay_Invoice.php');
        $this->ecpayInvoice = new Libs\EcpayInvoice ;
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
