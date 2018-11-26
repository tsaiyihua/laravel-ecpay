<?php
namespace TsaiYiHua\ECPay\Constants;

/**
 * 字軌類別
 */
class ECPayInvType
{
    // 一般稅額
    const General = '07';

    // 特種稅額
    const Special = '08';

    /**
     * @return \Illuminate\Support\Collection
     */
    static public function getConstantValues()
    {
        return collect([
            self::General,
            self::Special
        ])->unique();
    }
}