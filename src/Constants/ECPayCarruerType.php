<?php
namespace TsaiYiHua\ECPay\Constants;

/**
 * 電子發票載具類別
 */
class ECPayCarruerType
{
    // 無載具
    const None = '';

    // 會員載具
    const Member = '1';

    // 買受人自然人憑證
    const Citizen = '2';

    // 買受人手機條碼
    const Cellphone = '3';

    /**
     * @return \Illuminate\Support\Collection
     */
    static public function getConstantValues()
    {
        return collect([
            self::None,
            self::Member,
            self::Citizen,
            self::Cellphone
        ])->unique();
    }
}