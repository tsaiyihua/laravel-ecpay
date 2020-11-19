<?php
namespace TsaiYiHua\ECPay\Services\Invoice;

use TsaiYiHua\ECPay\Exceptions\ECPayException;
use TsaiYiHua\ECPay\Services\ECPayService;

/**
 *  G查詢作廢發票
 */
class ECPay_INVOICE_VOID_SEARCH implements ECPayService
{
    // 所需參數
    public $parameters = array(
        'TimeStamp'		=>'',
        'MerchantID'		=>'',
        'RelateNumber'		=>'',
        'CheckMacValue'		=>''
    );

    // 需要做urlencode的參數
    public $urlencode_field = array(
        'Reason' 		=>''
    );

    // 不需要送壓碼的欄位
    public $none_verification = array(
        'Reason' 		=>'',
        'CheckMacValue'		=>''
    );

    /**
     * 1寫入參數
     */
    public function insertString(array $arParameters): array
    {
        foreach ($this->parameters as $key => $value) {
            if(isset($arParameters[$key])) {
                $this->parameters[$key] = $arParameters[$key];
            }
        }

        return $this->parameters ;
    }

    /**
     * 2-2 驗證參數格式
     */
    public function checkExtendString(array $arParameters): array
    {

        $arErrors = array();

        // 4.廠商自訂編號

        // *預設不可為空值
        if(strlen($arParameters['RelateNumber']) == 0) {
            array_push($arErrors, '4:RelateNumber is required.');
        }

        // *預設最大長度為30碼
        if(strlen($arParameters['RelateNumber']) > 30) {
            array_push($arErrors, '4:RelateNumber max langth as 30.');
        }

        if(sizeof($arErrors)>0) {
            throw new ECPayException(join('<br>', $arErrors));
        }

        return $arParameters ;
    }

    /**
     * 4欄位例外處理方式(送壓碼前)
     */
    public function checkException(array $arParameters): array
    {
        return $arParameters ;
    }
}
