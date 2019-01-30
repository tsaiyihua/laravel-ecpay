<?php
/**
 * Created by PhpStorm.
 * User: yihua
 * Date: 2018/12/6
 * Time: 下午 3:05
 */

namespace TsaiYiHua\ECPay\Collections;


use Illuminate\Support\Collection;
use TsaiYiHua\ECPay\Exceptions\ECPayException;

class InvoiceResponseCollection extends Collection
{
    use CollectionTrait;

    protected $status;
    protected $message;

    /**
     * @param $response
     * @return $this
     * @throws ECPayException
     */
    public function collectResponse($response)
    {
        if (!isset($response['RtnCode'])) {
            throw new ECPayException('Error Response type');
        }
        $this->status = $response['RtnCode'];
        $this->message = $response['RtnMsg'];
        $allParams = collect(self::invoiceInfo())->unique();
        $allParams->each(function($param) use($response) {
            if (isset($response[$param])) {
                $this->put($param, $response[$param]);
            }
        });
        return $this;
    }

    static public function invoiceInfo()
    {
        return [
            'InvoiceNumber', 'InvoiceDate', 'RandomNumber', 'CheckMacValue'
        ];
    }
}