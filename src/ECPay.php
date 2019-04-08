<?php
/**
 * Created by PhpStorm.
 * User: yihua
 * Date: 2019/1/30
 * Time: 下午 4:31
 */

namespace TsaiYiHua\ECPay;


class ECPay
{
    public static $useECPayRoute = true;

    public static $sendForm = null;

    public static function ignoreRoutes()
    {
        static::$useECPayRoute = false;
        return new static;
    }
}