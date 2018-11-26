<?php
namespace TsaiYiHua\ECPay\Constants;

/**
 * 信用卡訂單處理動作資訊。
 */
class ECPayActionType
{
    /**
     * 關帳
     */
    const C = 'C';
    /**
     * 退刷
     */
    const R = 'R';
    /**
     * 取消
     */
    const E = 'E';
    /**
     * 放棄
     */
    const N = 'N';

    /**
     * @return \Illuminate\Support\Collection
     */
    static public function getConstantValues()
    {
        return collect([
            self::C,
            self::R,
            self::E,
            self::N
        ])->unique();
    }
}