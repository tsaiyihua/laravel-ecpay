<?php
namespace TsaiYiHua\ECPay\Constants;

/**
 * 電子發票開立註記。
 */
class ECPayInvoiceState
{
    /**
     * 需要開立電子發票。
     */
    const Yes = 'Y';
    /**
     * 不需要開立電子發票。
     */
    const No = '';

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