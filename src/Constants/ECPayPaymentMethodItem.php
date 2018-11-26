<?php
namespace TsaiYiHua\ECPay\Constants;

/**
 * 付款方式子項目。
 */
class ECPayPaymentMethodItem
{
    /**
     * 不指定。
     */
    const None = '';
    // WebATM 類(001~100)
    /**
     * 台新銀行。
     */
    const WebATM_TAISHIN = 'TAISHIN';
    /**
     * 玉山銀行。
     */
    const WebATM_ESUN = 'ESUN';
    /**
     * 華南銀行。
     */
    const WebATM_HUANAN = 'HUANAN';
    /**
     * 台灣銀行。
     */
    const WebATM_BOT = 'BOT';
    /**
     * 台北富邦。
     */
    const WebATM_FUBON = 'FUBON';
    /**
     * 中國信託。
     */
    const WebATM_CHINATRUST = 'CHINATRUST';
    /**
     * 第一銀行。
     */
    const WebATM_FIRST = 'FIRST';
    /**
     * 國泰世華。
     */
    const WebATM_CATHAY = 'CATHAY';
    /**
     * 兆豐銀行。
     */
    const WebATM_MEGA = 'MEGA';
    /**
     * 元大銀行。
     */
    const WebATM_YUANTA = 'YUANTA';
    /**
     * 土地銀行。
     */
    const WebATM_LAND = 'LAND';
    // ATM 類(101~200)
    /**
     * 台新銀行。
     */
    const ATM_TAISHIN = 'TAISHIN';
    /**
     * 玉山銀行。
     */
    const ATM_ESUN = 'ESUN';
    /**
     * 華南銀行。
     */
    const ATM_HUANAN = 'HUANAN';
    /**
     * 台灣銀行。
     */
    const ATM_BOT = 'BOT';
    /**
     * 台北富邦。
     */
    const ATM_FUBON = 'FUBON';
    /**
     * 中國信託。
     */
    const ATM_CHINATRUST = 'CHINATRUST';

    /**
     * 土地銀行。
     */
    const ATM_LAND = 'LAND';

    /**
     * 國泰世華銀行。
     */
    const ATM_CATHAY = 'CATHAY';
    /**
     * 大眾銀行。
     */
    const ATM_Tachong = 'Tachong';
    /**
     * 永豐銀行。
     */
    const ATM_Sinopac = 'Sinopac';
    /**
     * 彰化銀行。
     */
    const ATM_CHB = 'CHB';
    /**
     * 第一銀行。
     */
    const ATM_FIRST = 'FIRST';

    // 超商類(201~300)
    /**
     * 超商代碼繳款。
     */
    const CVS = 'CVS';
    /**
     * OK超商代碼繳款。
     */
    const CVS_OK = 'OK';
    /**
     * 全家超商代碼繳款。
     */
    const CVS_FAMILY = 'FAMILY';
    /**
     * 萊爾富超商代碼繳款。
     */
    const CVS_HILIFE = 'HILIFE';
    /**
     * 7-11 ibon代碼繳款。
     */
    const CVS_IBON = 'IBON';
    // 其他類(901~999)
    /**
     * 超商條碼繳款。
     */
    const BARCODE = 'BARCODE';
    /**
     * 信用卡(MasterCard/JCB/VISA)。
     */
    const Credit = 'Credit';
    /**
     * 貨到付款。
     */
    const COD = 'COD';

    /**
     * @return \Illuminate\Support\Collection
     */
    static public function getConstantValues()
    {
        return collect([
            self::None,
            self::WebATM_TAISHIN,
            self::WebATM_ESUN,
            self::WebATM_HUANAN,
            self::WebATM_BOT,
            self::WebATM_FUBON,
            self::WebATM_CHINATRUST,
            self::WebATM_FIRST,
            self::WebATM_CATHAY,
            self::WebATM_MEGA,
            self::WebATM_YUANTA,
            self::WebATM_LAND,
            self::ATM_TAISHIN,
            self::ATM_ESUN,
            self::ATM_HUANAN,
            self::ATM_BOT,
            self::ATM_FUBON,
            self::ATM_CHINATRUST,
            self::ATM_LAND,
            self::ATM_Tachong,
            self::ATM_Sinopac,
            self::ATM_CHB,
            self::ATM_FIRST,
            self::CVS,
            self::CVS_OK,
            self::CVS_FAMILY,
            self::CVS_HILIFE,
            self::CVS_IBON,
            self::BARCODE,
            self::Credit,
            self::COD
        ])->unique();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getWebATMItems()
    {
        return collect([
            self::WebATM_TAISHIN,
            self::WebATM_ESUN,
            self::WebATM_HUANAN,
            self::WebATM_BOT,
            self::WebATM_FUBON,
            self::WebATM_CHINATRUST,
            self::WebATM_FIRST,
            self::WebATM_CATHAY,
            self::WebATM_MEGA,
            self::WebATM_YUANTA,
            self::WebATM_LAND
        ])->unique();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getATMItems()
    {
        return collect([
            self::ATM_TAISHIN,
            self::ATM_ESUN,
            self::ATM_HUANAN,
            self::ATM_BOT,
            self::ATM_FUBON,
            self::ATM_CHINATRUST,
            self::ATM_LAND,
            self::ATM_Tachong,
            self::ATM_Sinopac,
            self::ATM_CHB,
            self::ATM_FIRST
        ])->unique();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getCVSItems()
    {
        return collect([
            self::None,
            self::CVS,
            self::CVS_OK,
            self::CVS_FAMILY,
            self::CVS_HILIFE,
            self::CVS_IBON
        ])->unique();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getOtherItems()
    {
        return collect([
            self::BARCODE,
            self::Credit,
            self::COD
        ])->unique();
    }
}