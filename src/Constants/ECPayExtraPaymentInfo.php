<?php
namespace TsaiYiHua\ECPay\Constants;

/**
 * 額外付款資訊。
 */
class ECPayExtraPaymentInfo
{
    /**
     * 需要額外付款資訊。
     */
    const Yes = 'Y';
    /**
     * 不需要額外付款資訊。
     */
    const No = 'N';

    /**
     * @return \Illuminate\Support\Collection
     */
    static public function getConstantValues()
    {
        return collect([
            self::Yes,
            self::No
        ])->unique();
    }
}