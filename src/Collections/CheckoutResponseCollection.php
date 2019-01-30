<?php
namespace TsaiYiHua\ECPay\Collections;

use Illuminate\Support\Collection;
use TsaiYiHua\ECPay\Exceptions\ECPayException;

class CheckoutResponseCollection extends Collection
{
    use CollectionTrait;

    protected $status;
    protected $message;
    protected $merchantId;

    /**
     * @param $response
     * @return $this
     * @throws ECPayException
     */
    public function collectResponse($response)
    {
        if (!isset($response['RtnCode'])) {
            throw new ECPayException('Error Response type');
        }
        $this->status = $response['RtnCode'];
        $this->message = $response['RtnMsg'];
        $this->merchantId = $response['MerchantID'];
        $allParams = collect(array_merge(
            self::instant(), self::nonInstant(), self::atm(), self::cvs(),
            self::installment(), self::extraInfo()
        ))->unique();
        $allParams->each(function($param) use($response) {
            if (isset($response[$param])) {
                $this->put($param, $response[$param]);
            }
        });
        return $this;
    }

    static public function instant()
    {
        return [
            'MerchantID', 'MerchantTradeNo', 'StoreID', 'TradeNo', 'TradeAmt', 'PaymentDate',
            'PaymentType', 'PaymentTypeChargeFee', 'TradeDate', 'SimulatePaid',
            'CustomField1', 'CustomField2', 'CustomField3', 'CustomField4', 'CheckMacValue'
        ];
    }

    static public function nonInstant()
    {
        return [
            'MerchantID', 'MerchantTradeNo', 'StoreID', 'TradeNo', 'TradeAmt', 'PaymentType', 'TradeDate',
            'CustomField1', 'CustomField2', 'CustomField3', 'CustomField4', 'CheckMacValue'
        ];
    }

    static public function atm()
    {
        return [
            'BankCode', 'vAccount', 'ExpireDate'
        ];
    }

    static public function cvs()
    {
        return [
            'PaymentNo', 'ExpireDate', 'Barcode1', 'Barcode2', 'Barcode3'
        ];
    }

    static public function installment()
    {
        return [
            'MerchantID', 'MerchantTradeNo', 'StoreID', 'CustomField1', 'CustomField2',
            'CustomField3', 'CustomField4', 'PeriodType', 'Frequency', 'ExecTimes', 'Amount',
            'Gwsr', 'ProcessDate', 'AuthCode', 'FirstAuthAmount', 'TotalSuccessTimes',
            'SimulatePaid', 'CheckMacValue'
        ];
    }

    static public function extraInfo()
    {
        return [
           'WebATMAccBank', 'WebATMAccNo', 'WebATMBankName', 'ATMAccBank', 'ATMAccNo', 'PaymentNo',
            'PayFrom', 'gwsr', 'process_date', 'auth_code', 'amount', 'stage', 'stast', 'staed',
            'eci', 'card4no', 'card6no', 'red_dan', 'red_de_amt', 'red_ok_amt', 'red_yet', 'PeriodType',
            'Frequency', 'ExecTimes', 'PeriodAmount', 'TotalSuccessTimes', 'TotalSuccessAmount'
        ];
    }
}