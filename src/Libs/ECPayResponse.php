<?php

namespace TsaiYiHua\ECPay\Libs;


use Illuminate\Support\Facades\App;
use MirrorFiction\Payment\Services\Donate\DonateService;
use TsaiYiHua\ECPay\Exceptions\ECPayException;
use TsaiYiHua\ECPay\Services\ECPayInvoiceServiceFactory;
use TsaiYiHua\ECPay\Services\ECPayService;

class ECPayResponse
{
    // 發票物件
    /** @var DonateService */
    public static $objReturn ;

    /**
     * 取得 Response 資料
     *
     * @param  array $merchantInfo
     * @param  array $parameters
     * @return array
     */
    static function response($merchantInfo = [], $parameters = [])
    {
        ECPayInvoiceServiceFactory::create(ECPayService::class, $merchantInfo['method']);
        self::$objReturn = App::make(ECPayService::class);

        // 壓碼檢查
        $parametersTmp = $parameters ;
        unset($parametersTmp['CheckMacValue']);
        $checkMacValue = ECPay_Invoice_CheckMacValue::generate(
            $parametersTmp,
            $merchantInfo['hashKey'],
            $merchantInfo['hashIv']
        );

        if($checkMacValue != $parameters['CheckMacValue']){
            throw new ECPayException('注意：壓碼錯誤');
        }

        return $parameters ;
    }
}
