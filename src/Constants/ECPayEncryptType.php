<?php
namespace TsaiYiHua\ECPay\Constants;


class ECPayEncryptType
{
    // MD5(預設)
    const ENC_MD5 = 0;

    // SHA256
    const ENC_SHA256 = 1;

    /**
     * @return \Illuminate\Support\Collection
     */
    static public function getConstantValues()
    {
        return collect([
            self::ENC_MD5,
            self::ENC_SHA256
        ])->unique();
    }
}