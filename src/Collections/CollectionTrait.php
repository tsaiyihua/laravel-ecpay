<?php
/**
 * Created by PhpStorm.
 * User: yihua
 * Date: 2019/1/30
 * Time: 下午 3:49
 */

namespace TsaiYiHua\ECPay\Collections;


trait CollectionTrait
{
    public function getStatus()
    {
        return $this->status;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getItems()
    {
        return $this->items;
    }
}