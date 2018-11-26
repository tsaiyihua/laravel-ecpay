<?php
namespace TsaiYiHua\ECPay;

use Illuminate\Support\Collection;
use TsaiYiHua\ECPay\Exceptions\ECPayException;
use TsaiYiHua\ECPay\Services\StringService;

trait ECPayTrait
{
    /**
     * Send data to ECPay
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws ECPayException
     */
    public function send()
    {
        /** @var Collection $this->postData */
        $this->postData = $this->postData->filter(function($data){
            return !($data==='');
        });
        $this->setCheckCodeValue();
        $data = [
            'apiUrl' => $this->apiUrl,
            'postData' => $this->postData
        ];
        return view('ecpay::send', $data);
    }

    /**
     * Set CheckMacValue to postData
     * @throws ECPayException
     */
    protected function setCheckCodeValue()
    {
        /** @var Collection $this->postData */
        if ($this->postData->isEmpty()) {
            throw new ECPayException('Post Data is Empty');
        }
        /** @var Collection $this->postData */
        $checkValue = StringService::checkMacValueGenerator($this->postData->toArray());
        /** @var Collection $this->postData */
        $this->postData->put('CheckMacValue', $checkValue);
    }
}