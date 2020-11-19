<?php


namespace TsaiYiHua\ECPay\Constants;


class ECPayInvoiceTagType
{
    // 發票開立
    const Invoice = 'I';

    // 發票作廢
    const Invoice_Void = 'II';

    // 折讓開立
    const Allowance = 'A';

    // 折讓作廢
    const Allowance_Void = 'AI';

    // 發票中獎
    const Invoice_Winning = 'AW';

    /**
     * @return \Illuminate\Support\Collection
     */
    static public function getConstantValues()
    {
        return collect([
            self::Invoice,
            self::Invoice_Void,
            self::Allowance,
            self::Allowance_Void,
            self::Invoice_Winning
        ])->unique();
    }
}
