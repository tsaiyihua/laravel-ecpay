<?php


namespace TsaiYiHua\ECPay\Constants;


class ECPayNotifyType
{
    // 簡訊通知
    const Sms = 'S';

    // 電子郵件通知
    const Email = 'E';

    // 皆通知
    const All = 'A';

    /**
     * @return \Illuminate\Support\Collection
     */
    static public function getConstantValues()
    {
        return collect([
            self::Sms,
            self::Email,
            self::All
        ])->unique();
    }
}
