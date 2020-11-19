<?php


namespace TsaiYiHua\ECPay\Constants;


class ECPayNotifiedType
{
    // 通知客戶
    const Customer = 'C';

    // 通知廠商
    const vendor = 'M';

    // 皆發送
    const All = 'A';

    /**
     * @return \Illuminate\Support\Collection
     */
    static public function getConstantValues()
    {
        return collect([
            self::Customer,
            self::vendor,
            self::All
        ])->unique();
    }
}
