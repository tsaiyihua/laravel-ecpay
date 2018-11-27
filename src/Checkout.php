<?php
namespace TsaiYiHua\ECPay;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use TsaiYiHua\ECPay\Exceptions\ECPayException;
use TsaiYiHua\ECPay\Services\StringService;
use TsaiYiHua\ECPay\Validations\InstallmentValidation;
use TsaiYiHua\ECPay\Validations\InvoiceValidation;
use TsaiYiHua\ECPay\Validations\ItemValidation;
use TsaiYiHua\ECPay\Validations\PaymentValidation;
use TsaiYiHua\ECPay\Validations\PeriodAmountValidator;

class Checkout
{
    use ECPayTrait;

    protected $apiUrl;
    protected $postData;
    protected $platform;
    protected $merchantId;
    protected $hashKey;
    protected $hashIv;
    protected $encryptType='sha256';
    protected $notifyUrl;

    protected $itemValidation;

    public function __construct(ItemValidation $itemValidation)
    {
        if (config('app.env') == 'production') {
            $this->apiUrl = 'https://payment.ecpay.com.tw/Cashier/AioCheckOut/V5';
        } else {
            $this->apiUrl = 'https://payment-stage.ecpay.com.tw/Cashier/AioCheckOut/V5';
        }
        $this->postData = new Collection();

        $this->merchantId = config('ecpay.MerchantId');
        $this->hashKey = config('ecpay.HashKey');
        $this->hashIv = config('ecpay.HashIV');
        $this->notifyUrl = route('ecpay.notify');

        $this->itemValidation = $itemValidation;
    }

    /**
     * Handle the Post Data
     * @param $data
     * @return $this
     * @throws ECPayException
     */
    public function setPostData($data)
    {
        $validator = PaymentValidation::postDataValidator($data);
        if ($validator->fails()) {
            throw new ECPayException($validator->getMessageBag());
        }
        if (isset($data['ItemName']) && isset($data['TotalAmount'])) {
            $itemNamePayment = $data['ItemName'];
            $amount = $data['TotalAmount'];
        } else {
            $items = $data['Items'];
            $validateItem = $this->itemValidation->ItemValidation($items);
            $amount = 0;
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
        $this->postData->put('MerchantID', $this->merchantId);
        $this->postData->put('MerchantTradeNo', $data['OrderId']??StringService::identifyNumberGenerator('O'));
        $this->postData->put('MerchantTradeDate', Carbon::now()->format('Y/m/d H:i:s'));
        $this->postData->put('PaymentType', 'aio');
        $this->postData->put('TotalAmount' ,$amount);
        $this->postData->put('TradeDesc' , urlencode($data['ItemDescription']));
        $this->postData->put('ItemName' , $itemNamePayment);
        $this->postData->put('ReturnURL', $this->notifyUrl);
        $this->postData->put('ChoosePayment', $data['PaymentMethod']);
        $this->postData->put('EncryptType' ,1); // 一律使用 SHA256 加密
        $optionParams = [
            'StoreId', 'ClientBackURL', 'ItemURL', 'Remark', 'ChooseSubPayment', 'OrderResultURL',
            'NeedExtraPaidInfo', 'IgnorePayment', 'PlatformID', 'InvoiceMark', 'CustomField1',
            'CustomField2', 'CustomField3', 'CustomField4'
        ];
        foreach($optionParams as $param) {
            if (isset($data[$param]) && !empty($data[$param])) {
                $this->postData->put($param, $data[$param]);
            }
        }

        if ($data['PaymentMethod'] == 'Credit' || $data['PaymentMethod'] == 'ALL') {
            $this->postData->put('BindingCard', (isset($data['BindingCard'])) ? $data['BindingCard']:0);
            if (isset($data['PlatformID'])) {
                $this->postData->put('MerchantMemberID', $data['PlatformID'] . $data['UserId']);
            } else {
                $this->postData->put('MerchantMemberID', $this->merchantId . $data['UserId']);
            }
            if ($data['PaymentMethod'] == 'Credit' && isset($data['Language'])) {
                $this->postData->put('Language', $data['Language']);
            }
            if (isset($data['CreditInstallment'])) {
                $this->postData->put('CreditInstallment', $data['CreditInstallment']);
            } else {
                if (isset($data['Redeem'])) {
                    $this->postData->put('Redeem', $data['Redeem']);
                }
                if (config('app.env') == 'production') {
                    $this->postData->put('UnionPay', (isset($data['UnionPay'])) ? $data['UnionPay'] : 0);
                }
            }
        }
        return $this;
    }

    /**
     * 還沒用到
     * @param string $platformId
     * @return $this
     * @throws ECPayException
     */
    public function setPlatform($platformId)
    {
        if (strlen($platformId) > 10) {
            throw new ECPayException('PlatformId max length is 10');
        }
        $this->platform = $platformId;
        $this->postData->put('PlatformId', $platformId);
        return $this;
    }

    /**
     * @param string $installmentData
     * @return $this;
     * @throws ECPayException
     */
    public function withInstallment($installmentData)
    {
        $validator = InstallmentValidation::installmentValidator($installmentData);
        if ($validator->fails()) {
            throw new ECPayException($validator->getMessageBag());
        }
        $this->postData->put('CreditInstallment', $installmentData);
        return $this;
    }

    /**
     * @param $data
     * @return $this
     * @throws ECPayException
     */
    public function withPeriodAmount($data)
    {
        $validator = PeriodAmountValidator::periodAmtValidator($data);
        if ($validator->fails()) {
            throw new ECPayException($validator->getMessageBag());
        }
        $params = [
            'PeriodAmount', 'PeriodType', 'Frequency', 'ExecTimes', 'PeriodReturnURL'
        ];
        if ($data['PeriodAmount'] != $this->postData->get('TotalAmount')) {
            throw new ECPayException('PeriodAmount must the same as TotalAmount');
        }
        foreach($params as $param) {
            if (isset($data[$param]) && !empty($data[$param])) {
                $this->postData->put($param, $data[$param]);
            }
        }
        return $this;
    }
    /**
     * 當要開立發票時，帶入此方法
     * @param array $invData
     * @return $this
     * @throws ECPayException
     */
    public function withInvoice($invData)
    {
        $validator = InvoiceValidation::invoiceValidator($invData);
        $items = $invData['Items'];
        foreach($items as $item) {
            $itemName[] = $item['name'];
            $itemQty[] = $item['qty'];
            $itemUnit[] = $item['unit'];
            $itemPrice[] = $item['price'];
        }
        $itemNameInvoice = implode('|', $itemName);
        $itemCountInvoice = implode('|', $itemQty);
        $itemUnitInvoice = implode('|', $itemUnit);
        $itemPriceInvoice = implode('|', $itemPrice);

        $taxType = $invData['TaxType'] ?? 1;
        $delayDay = $invData['DelayDay'] ?? 0;
        $print = $invData['Print'] ?? 1;
        $carruerType = $invData['CarruerType'] ?? '';
        $donation = $invData['Donation'] ?? '';
        $invType = $invData['InvType'] ?? '07';
        $customerIdentifier = $invData['CustomerIdentifier'] ?? '';
        $customerName = StringService::replaceSymbol(urlencode($invData['CustomerName'] ?? ''));
        $customerAddress = StringService::replaceSymbol(urlencode($invData['CustomerAddr'] ?? ''));
        $customEmail = StringService::replaceSymbol(urlencode($invData['CustomerEmail'] ?? ''));
        $customPhone = $invData['CustomerPhone'] ?? '';

        $this->postData->put('InvoiceMark', 'Y');
        $this->postData->put('RelateNumber', $this->postData->get('MerchantTradeNo')??StringService::identifyNumberGenerator('O'));
        $this->postData->put('TaxType', (string)$taxType);
        $this->postData->put('InvoiceItemName', $itemNameInvoice);
        $this->postData->put('InvoiceItemCount', $itemCountInvoice);
        $this->postData->put('InvoiceItemWord', $itemUnitInvoice);
        $this->postData->put('InvoiceItemPrice', $itemPriceInvoice);
        $this->postData->put('DelayDay', $delayDay);
        $this->postData->put('InvType', $invType);
        $this->postData->put('Print', (string)$print);

        $this->postData->put('CustomerID', (string)$invData['UserId']);
        $this->postData->put('CustomerIdentifier', $customerIdentifier);
        if ($print == 0) {
            if (empty($carruerType) && empty($donation)) {
                $validator->getMessageBag()
                    ->add('CarruerType and Donation', 'CarruerType or Donation must be required while Print is 0');
            }
        } else {
            if (!empty($carruerType)) {
                $validator->getMessageBag()
                    ->add('CarruerType', 'CarruerType must be empty while Print is 1');
            }
            if (empty($customerName)) {
                $validator->getMessageBag()
                    ->add('CustomerName', 'CustomerName can not be empty while Print is 1');
            }
            if (empty($customerAddress)) {
                $validator->getMessageBag()
                    ->add('CustomerAddr', 'CustomerAddr can not be empty while Print is 1');
            }
            if (empty($customEmail) && empty($customPhone)) {
                $validator->getMessageBag()
                    ->add('CustomerEmail and CustomerPhone',
                        'CustomerEmail and CustomerPhone can not be empty at the same time while Print is 1');
            }
        }
        if (!empty($customerIdentifier)) {
            if ($carruerType == 1 || $carruerType == 2) {
                $validator->getMessageBag()
                    ->add('CarruerType', 'CarruerType cat not be 1 or 2 while CustomerIdentifier has value');
            }
            $donation = 0;
        }
        if (strlen($customerName) > 60) {
            $validator->getMessageBag()->add('CustomerName', 'CustomerName can not great then 60 characters');
        }
        $this->postData->put('CustomerName', $customerName);

        if (strlen($customerAddress) > 200) {
            $validator->getMessageBag()->add('CustomerAddr', 'CustomerAddr can not great then 200 characters');
        }
        $this->postData->put('CustomerAddr', $customerAddress);
        $this->postData->put('CustomerPhone', $customPhone);

        if (strlen($customEmail) > 200) {
            $validator->getMessageBag()->add('CustomerEmail', 'CustomerEmail can not great then 200 characters');
        }
        $this->postData->put('CustomerEmail', $customEmail);
        if ($taxType == '2') {
            $okValue = [1,2];
            $clearanceMark = $invData['ClearanceMark'] ?? '';
            if (in_array($clearanceMark, $okValue)) {
                $this->postData->put('ClearanceMark', $clearanceMark);
            } else {
                $validator->getMessageBag()
                    ->add('ClearanceMark', 'ClearanceMark must be 1 or 2 while TaxType is 2');
            }
        }
        if ($carruerType == 2 || $carruerType == 3) {
            $this->postData->put('CarruerNum', $invData['CarruerNum']);
        } else {
            $this->postData->put('CarruerNum', '');
        }
        $this->postData->put('Donation', $donation);
        if ($donation == 1) {
            $this->postData->put('LoveCode', $invData['LoveCode']);
        }
        if ( $validator->getMessageBag()->count() > 0 ) {
            throw new ECPayException($validator->getMessageBag());
        }
        return $this;
    }

    public function setNotifyUrl($url)
    {
        $this->notifyUrl = $url;
        return $this;
    }
}