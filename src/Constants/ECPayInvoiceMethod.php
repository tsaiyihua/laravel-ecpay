<?php


namespace TsaiYiHua\ECPay\Constants;


class ECPayInvoiceMethod
{
    // 一般開立發票。
    const INVOICE = 'INVOICE';

    // 延遲或觸發開立發票。
    const INVOICE_DELAY = 'INVOICE_DELAY';

    // 開立折讓。
    const ALLOWANCE = 'ALLOWANCE';

    // 線上開立折讓(通知開立)。
    const ALLOWANCE_BY_COLLEGIATE = 'ALLOWANCE_BY_COLLEGIATE';

    // 發票作廢。
    const INVOICE_VOID = 'INVOICE_VOID';

    // 折讓作廢。
    const ALLOWANCE_VOID = 'ALLOWANCE_VOID';

    // 查詢發票。
    const INVOICE_SEARCH = 'INVOICE_SEARCH';

    // 查詢作廢發票。
    const INVOICE_VOID_SEARCH = 'INVOICE_VOID_SEARCH';

    // 查詢折讓明細。
    const ALLOWANCE_SEARCH = 'ALLOWANCE_SEARCH';

    // 查詢折讓作廢明細。
    const ALLOWANCE_VOID_SEARCH = 'ALLOWANCE_VOID_SEARCH';

    // 發送通知。
    const INVOICE_NOTIFY = 'INVOICE_NOTIFY';

    // 付款完成觸發或延遲開立發票。
    const INVOICE_TRIGGER = 'INVOICE_TRIGGER';

    // 手機條碼驗證。
    const CHECK_MOBILE_BARCODE = 'CHECK_MOBILE_BARCODE';

    // 愛心碼驗證。
    const CHECK_LOVE_CODE = 'CHECK_LOVE_CODE';

    /**
     * @return \Illuminate\Support\Collection
     */
    static public function getConstantValues()
    {
        return collect([
            self::INVOICE,
            self::INVOICE_DELAY,
            self::ALLOWANCE,
            self::ALLOWANCE_BY_COLLEGIATE,
            self::INVOICE_VOID,
            self::ALLOWANCE_VOID,
            self::INVOICE_SEARCH,
            self::INVOICE_VOID_SEARCH,
            self::ALLOWANCE_SEARCH,
            self::ALLOWANCE_VOID_SEARCH,
            self::INVOICE_NOTIFY,
            self::INVOICE_TRIGGER,
            self::CHECK_MOBILE_BARCODE,
            self::CHECK_LOVE_CODE
        ])->unique();
    }
}
