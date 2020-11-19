<?php


namespace TsaiYiHua\ECPay\Services;


use Illuminate\Support\Facades\App;

class ECPayInvoiceServiceFactory
{
    static public function create($contract, $method = null)
    {
        /** bind service */
        $targetClass = __NAMESPACE__.'\\Invoice\\ECPay_'.$method;
        App::bind($contract, $targetClass);
    }
}
