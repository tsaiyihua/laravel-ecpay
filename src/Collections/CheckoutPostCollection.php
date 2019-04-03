<?php
namespace TsaiYiHua\ECPay\Collections;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use TsaiYiHua\ECPay\Exceptions\ECPayException;
use TsaiYiHua\ECPay\Services\StringService;
use TsaiYiHua\ECPay\Validations\InstallmentValidation;
use TsaiYiHua\ECPay\Validations\ItemValidation;
use TsaiYiHua\ECPay\Validations\PeriodAmountValidator;

class CheckoutPostCollection extends Collection
{
    public $merchantId;
    public $attributes;
    public $notifyUrl;
    public $returnUrl;

    public function __construct()
    {
        parent::__construct();
        $this->merchantId = config('ecpay.MerchantId');
        $this->notifyUrl = route('ecpay.notify');
        $this->returnUrl = route('ecpay.return');
    }

    public function setData($formData)
    {
        $this->attributes = $formData;
        return $this;
    }

    /**
     * @return $this
     * @throws ECPayException
     */
    public function setBasicInfo()
    {
        if (empty($this->attributes)) {
            throw new ECPayException('attributes must be set');
        }
        $this->put('MerchantID', $this->merchantId);
        $this->put('MerchantTradeDate', Carbon::now()->format('Y/m/d H:i:s'));
        $this->put('PaymentType', 'aio');
        $this->put('ReturnURL', $this->notifyUrl);
        $this->put('OrderResultURL', $this->returnUrl);
        $this->put('ChoosePayment', $this->attributes['PaymentMethod']);
        $this->put('EncryptType' ,1); // 一律使用 SHA256 加密
        return $this;
    }

    /**
     * @return $this
     * @throws ECPayException
     */
    public function setOrderInfo()
    {
        if (empty($this->attributes)) {
            throw new ECPayException('attributes must be set');
        }
        if (isset($this->attributes['ItemName']) && isset($this->attributes['TotalAmount'])) {
            $itemNamePayment = $this->attributes['ItemName'];
            $amount = $this->attributes['TotalAmount'];
        } else {
            $itemValidation = new ItemValidation;
            $items = $this->attributes['Items'];
            $validateItem = $itemValidation->ItemValidation($items);
            $amount = 0;
            $displayName = [];
            foreach ($items as $item) {
                $displayName[] = $item['name'] . 'x' . $item['qty'];
                $amount += $item['qty'] * $item['price'];
            }
            $itemNamePayment = implode('#', $displayName);

            if (strlen($itemNamePayment) > 200) {
                $validateItem->add('ItemName', 'Composed Item Name can not more then 200 characters');
            }

            if ($validateItem->count() > 0) {
                throw new ECPayException($validateItem->getMessageBag());
            }
        }
        $this->put('MerchantTradeNo', $this->attributes['OrderId'] ?? StringService::identifyNumberGenerator('O'));
        $this->put('TotalAmount' ,$amount);
        $this->put('TradeDesc' , urlencode($this->attributes['ItemDescription']));
        $this->put('ItemName' , $itemNamePayment);
        return $this;
    }

    /**
     * @return $this
     * @throws ECPayException
     */
    public function setOptionalInfo()
    {
        if (empty($this->attributes)) {
            throw new ECPayException('attributes must be set');
        }
        $optionParams = [
            'StoreId', 'ClientBackURL', 'ItemURL', 'Remark', 'ChooseSubPayment',
            'NeedExtraPaidInfo', 'IgnorePayment', 'PlatformID', 'InvoiceMark', 'CustomField1',
            'CustomField2', 'CustomField3', 'CustomField4', 'ExpireDate', 'PaymentInfoURL', 'ClientRedirectURL'
        ];
        foreach($optionParams as $param) {
            if (isset($this->attributes[$param]) && !empty($this->attributes[$param])) {
                $this->put($param, $this->attributes[$param]);
            }
        }
        return $this;
    }

    /**
     * @return $this
     * @throws ECPayException
     */
    public function optimize()
    {
        if (empty($this->attributes)) {
            throw new ECPayException('attributes must be set');
        }
        if ($this->attributes['PaymentMethod'] == 'Credit' || $this->attributes['PaymentMethod'] == 'ALL') {
            /** 欲使用 BindingCard、MerchantMemberID 這兩個參數功能,特店必須有會員系統。 */
            if (isset($this->attributes['UserId'])) {
                $this->put('BindingCard',
                    (isset($this->attributes['BindingCard'])) ? $this->attributes['BindingCard'] : 0
                );
                if (isset($this->attributes['PlatformID'])) {
                    $this->put('MerchantMemberID', $this->attributes['PlatformID'] . $this->attributes['UserId']);
                } else {
                    $this->put('MerchantMemberID', $this->merchantId . $this->attributes['UserId']);
                }
            }
            if ($this->attributes['PaymentMethod'] == 'Credit' && isset($this->attributes['Language'])) {
                $this->put('Language', $this->attributes['Language']);
            }
            if (isset($this->attributes['CreditInstallment'])) {
                $this->put('CreditInstallment', $this->attributes['CreditInstallment']);
            } else {
                if (isset($this->attributes['Redeem'])) {
                    $this->put('Redeem', $this->attributes['Redeem']);
                }
                if (config('app.env') == 'production') {
                    $this->put('UnionPay', (isset($this->attributes['UnionPay'])) ? $this->attributes['UnionPay'] : 0);
                }
            }
        }
        return $this;
    }

    /**
     * @param string $installmentData
     * @return $this;
     * @throws ECPayException
     */
    public function setInstallment($installmentData)
    {
        $validator = InstallmentValidation::installmentValidator($installmentData);
        if ($validator->fails()) {
            throw new ECPayException($validator->getMessageBag());
        }
        $this->put('CreditInstallment', $installmentData);
        return $this;
    }

    /**
     * @param $data
     * @return $this
     * @throws ECPayException
     */
    public function setPeriodAmount($data)
    {
        $validator = PeriodAmountValidator::periodAmtValidator($data);
        if ($validator->fails()) {
            throw new ECPayException($validator->getMessageBag());
        }
        $params = [
            'PeriodAmount', 'PeriodType', 'Frequency', 'ExecTimes', 'PeriodReturnURL'
        ];
        if ($data['PeriodAmount'] != $this->get('TotalAmount')) {
            throw new ECPayException('PeriodAmount must the same as TotalAmount');
        }
        foreach($params as $param) {
            if (isset($data[$param]) && !empty($data[$param])) {
                $this->put($param, $data[$param]);
            }
        }
        return $this;
    }
}