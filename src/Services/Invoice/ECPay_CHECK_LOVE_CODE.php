<?php
namespace TsaiYiHua\ECPay\Services\Invoice;

use TsaiYiHua\ECPay\Exceptions\ECPayException;
use TsaiYiHua\ECPay\Services\ECPayService;

/**
 *  M愛心碼驗證
 */
class ECPay_CHECK_LOVE_CODE implements ECPayService
{
    // 所需參數
    public $parameters = array(
        'TimeStamp'		=>'',
        'MerchantID'		=>'',
        'LoveCode'		=>'',
        'CheckMacValue'		=>''
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

        // 51.LoveCode愛心碼
        // *必填 3-7碼
        if( strlen($arParameters['LoveCode']) > 7 ) {
            array_push($arErrors, "51:LoveCode max length as 7.");
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
