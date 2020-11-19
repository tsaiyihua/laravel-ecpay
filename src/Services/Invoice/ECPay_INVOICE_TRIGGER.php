<?php
namespace TsaiYiHua\ECPay\Services\Invoice;

use TsaiYiHua\ECPay\Constants\ECPayPayTypeCategory;
use TsaiYiHua\ECPay\Exceptions\ECPayException;
use TsaiYiHua\ECPay\Services\ECPayService;

/**
 *  K付款完成觸發或延遲開立發票
 */
class ECPay_INVOICE_TRIGGER implements ECPayService
{
    // 所需參數
    public $parameters = array(
        'TimeStamp'		=>'',
        'MerchantID'		=>'',
        'CheckMacValue'		=>'',
        'Tsr'			=>'',
        'PayType'		=> 2
    );

    // 需要做urlencode的參數
    public $urlencode_field = array(
    );

    // 不需要送壓碼的欄位
    public $none_verification = array(
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

        // 33.交易單號 Tsr
        // *必填項目
        if(strlen($arParameters['Tsr']) == 0 ) {
            array_push($arErrors, '33:Tsr is required.');
        }

        // *判斷最大字元是否超過30字
        if (strlen($arParameters['Tsr']) > 30) {
            array_push($arErrors, '33:Tsr max length as 30.');
        }

        // 34.交易類別 PayType
        // *2016-10-4 修改為僅允許 2
        if( $arParameters['PayType'] != ECPayPayTypeCategory::Ecpay) {
            array_push($arErrors, "34:Invalid PayType.");
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
