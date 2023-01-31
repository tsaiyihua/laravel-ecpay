<?php
namespace TsaiYiHua\ECPay\Services;

use Exception;
use TsaiYiHua\ECPay\Exceptions\ECPayException;

class StringService
{
    /**
     * Identify Number Generator
     * @param string $prefix
     * @return string
     * @throws ECPayException
     */
    static public function identifyNumberGenerator(string $prefix='A'): string
    {
        if (strlen($prefix) > 2) {
            throw new ECPayException('ID prefix character maximum is 2 characters');
        }
        $intMsConst = 1000000;
        try {
            list($ms, $timestamp) = explode(" ", microtime());
            $msString = substr('000000'.($ms*$intMsConst), -6);
            return $prefix . $timestamp . $msString . substr('00'.random_int(0, 99),-2);
        } catch (Exception) {
            return $prefix . $timestamp . $msString . '00';
        }
    }

    /**
     * @param array $data
     * @param array $hashData
     * @return string
     */
    static public function checkMacValueGenerator(array $data, array $hashData=[]): string
    {
        if (empty($hashData)) {
            $hashData['key'] = config('ecpay.HashKey');
            $hashData['iv'] = config('ecpay.HashIV');
            $hashData['type'] = 'sha256';
        }
        if (isset($hashData['ignore'])) {
            foreach($hashData['ignore'] as $field) {
                unset($data[$field]);
            }
        }
        uksort($data, array(self::class, 'merchantSort'));

        $checkCodeStr = 'HashKey='.$hashData['key'];
        foreach($data as $key=>$val) {
            $checkCodeStr .= '&'.$key.'='.$val;
        }
        $checkCodeStr .= '&HashIV='.$hashData['iv'];
        if ($hashData['type'] === 'md5') {
            $checkCodeStr = self::replaceSymbol(strtolower(urlencode($checkCodeStr)));
            return strtoupper(md5($checkCodeStr));
        } else {
            $checkCodeStr = self::replaceSymbol(urlencode($checkCodeStr));
            return strtoupper(hash($hashData['type'], strtolower($checkCodeStr)));
        }
    }

    /**
     * 參數內特殊字元取代
     * @param string $sParameters
     * @return string
     */
    static public function replaceSymbol(string $sParameters): string
    {
        if(!empty($sParameters)){
            $sParameters = str_replace('%2D', '-', $sParameters);
            $sParameters = str_replace('%2d', '-', $sParameters);
            $sParameters = str_replace('%5F', '_', $sParameters);
            $sParameters = str_replace('%5f', '_', $sParameters);
            $sParameters = str_replace('%2E', '.', $sParameters);
            $sParameters = str_replace('%2e', '.', $sParameters);
            $sParameters = str_replace('%21', '!', $sParameters);
            $sParameters = str_replace('%2A', '*', $sParameters);
            $sParameters = str_replace('%2a', '*', $sParameters);
            $sParameters = str_replace('%28', '(', $sParameters);
            $sParameters = str_replace('%29', ')', $sParameters);
        }
        return $sParameters;
    }

    /**
     * 自訂排序使用
     * @param $a
     * @param $b
     * @return int
     */
    private static function merchantSort($a,$b): int
    {
        return strcasecmp($a, $b);
    }
}
