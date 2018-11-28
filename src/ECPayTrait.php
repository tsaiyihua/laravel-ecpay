<?php
namespace TsaiYiHua\ECPay;

use GuzzleHttp\Client;
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
     * Using CURL to send form data (For Query Info)
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws ECPayException
     */
    public function query()
    {
        /** @var Collection $this->postData */
        $this->postData = $this->postData->filter(function($data){
            return !($data==='');
        });
        $this->setCheckCodeValue();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($this->postData->toArray()));

        // 回傳參數
        $response = curl_exec($ch);
        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpStatus == 200) {
            return $this->parseResponse($response);
        } else {
            throw new ECPayException('HTTP Error with code '.$httpStatus);
        }
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
        $hashData = [
            'key' => $this->hashKey,
            'iv' => $this->hashIv,
            'type' => $this->encryptType
        ];
        if (isset($this->checkMacValueIgnoreFields)) {
            $hashData['ignore'] = $this->checkMacValueIgnoreFields;
        }
        /** @var Collection $this->postData */
        $checkValue = StringService::checkMacValueGenerator($this->postData->toArray(), $hashData);
        /** @var Collection $this->postData */
        $this->postData->put('CheckMacValue', $checkValue);
    }

    /**
     * @param $response
     * @return Collection
     * @throws ECPayException
     */
    protected function parseResponse($response)
    {
        $responseCollection = new Collection();
        preg_match_all('/([^&]*=[^&]*)/', $response, $match);
        if (!empty($match[0])) {
            foreach($match[0] as $paramValue) {
                $param = strstr($paramValue, '=', true);
                $value = substr(strstr($paramValue,'='), 1, 255);
                $responseCollection->put($param, $value);
            }
        } else {
            throw new ECPayException($response);
        }
        return $responseCollection;
    }
}