<?php


namespace TsaiYiHua\ECPay\Constants;


class ECPayPayTypeCategory
{
    const Ecpay = '2';

    /**
     * @return \Illuminate\Support\Collection
     */
    static public function getConstantValues()
    {
        return collect([
            self::Ecpay
        ])->unique();
    }
}
