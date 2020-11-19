<?php
namespace TsaiYiHua\ECPay\Services;

interface ECPayService
{
    /**
     * 1寫入參數
     * @param array $arParameters
     * @return array
     */
    public function insertString(array $arParameters) : array;

    /**
     * 2-2 驗證參數格式
     * @param array $arParameters
     * @return array
     */
    public function checkExtendString(array $arParameters) : array;

    /**
     * 4欄位例外處理方式(送壓碼前)
     * @param array $arParameters
     * @return array
     */
    public function checkException(array $arParameters) : array;
}
