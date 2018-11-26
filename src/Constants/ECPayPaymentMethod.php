<?php
namespace TsaiYiHua\ECPay\Constants;

/**
 * 付款方式。
 */
class ECPayPaymentMethod
{
    /**
     * 不指定付款方式。
     */
    const ALL = 'ALL';
    /**
     * 信用卡付費。
     */
    const Credit = 'Credit';
    /**
     * 網路 ATM。
     */
    const WebATM = 'WebATM';
    /**
     * 自動櫃員機。
     */
    const ATM = 'ATM';
    /**
     * 超商代碼。
     */
    const CVS = 'CVS';
    /**
     * 超商條碼。
     */
    const BARCODE = 'BARCODE';
    /**
     * AndroidPay。(同 GooglePay)
     */
    const AndroidPay = 'GooglePay';
    /**
     * GooglePay。
     */
    const GooglePay = 'GooglePay';

    /**
     * @return \Illuminate\Support\Collection
     */
    static public function getConstantValues()
    {
        return collect([
            self::ALL,
            self::Credit,
            self::AndroidPay,
            self::WebATM,
            self::ATM,
            self::BARCODE,
            self::CVS,
            self::AndroidPay,
            self::GooglePay
        ])->unique();
    }

    /**
     * @param string $method
     * @return bool
     */
    static public function isInstantMethod($method)
    {
        $instantMethod = [self::Credit, self::WebATM, self::AndroidPay, self::GooglePay];
        if (in_array($method, $instantMethod)) {
            return true;
        } else {
            return false;
        }
    }
}