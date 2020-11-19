<?php


namespace TsaiYiHua\ECPay\Constants;


class ECPayAllowanceNotifyType
{
    // 簡訊通知
    const Sms = 'S';

    // 電子郵件通知
    const Email = 'E';

    // 皆通知
    const All = 'A';

    // 皆不通知
    const None = 'N';

    /**
     * @return \Illuminate\Support\Collection
     */
    static public function getConstantValues()
    {
        return collect([
            self::Sms,
            self::Email,
            self::All,
            self::None
        ])->unique();
    }
}
