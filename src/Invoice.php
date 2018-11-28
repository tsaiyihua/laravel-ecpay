<?php
/**
 * Created by PhpStorm.
 * User: yihua
 * Date: 2018/11/27
 * Time: 下午 3:30
 */

namespace TsaiYiHua\ECPay;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use TsaiYiHua\ECPay\Exceptions\ECPayException;
use TsaiYiHua\ECPay\Services\StringService;
use TsaiYiHua\ECPay\Validations\InvoiceValidation;

class Invoice
{
    use ECPayTrait;

    protected $apiUrl;
    protected $postData;
    protected $merchantId;
    protected $hashKey;
    protected $hashIv;
    protected $encryptType='md5';

    protected $checkMacValueIgnoreFields;

    public function __construct()
    {
        if (config('app.env') == 'production') {
            $this->apiUrl = 'https://einvoice.ecpay.com.tw/Invoice/Issue';
        } else {
            $this->apiUrl = 'https://einvoice-stage.ecpay.com.tw/Invoice/Issue';
        }
        $this->postData = new Collection();

        $this->merchantId = config('ecpay.MerchantId');
        $this->hashKey = config('ecpay.InvoiceHashKey');
        $this->hashIv = config('ecpay.InvoiceHashIV');
        $this->checkMacValueIgnoreFields = [
            'InvoiceRemark', 'ItemName', 'ItemWord', 'ItemRemark', 'CheckMacValue'
        ];
    }

    /**
     * @param $invData
     * @return $this
     * @throws Exceptions\ECPayException
     */
    public function setPostData($invData)
    {
        $validator = InvoiceValidation::invoiceValidator($invData);
        $items = $invData['Items'];
        $amount = 0;
        foreach($items as $item) {
            $itemName[] = $item['name'];
            $itemQty[] = $item['qty'];
            $itemUnit[] = $item['unit'];
            $itemPrice[] = $item['price'];
            $itemAmount[] = $item['price']*$item['qty'];
            $amount += $item['price']*$item['qty'];
        }
        $itemNameInvoice = implode('|', $itemName);
        $itemCountInvoice = implode('|', $itemQty);
        $itemUnitInvoice = implode('|', $itemUnit);
        $itemPriceInvoice = implode('|', $itemPrice);
        $itemAmountInvoice = implode('|', $itemAmount);

        $taxType = $invData['TaxType'] ?? 1;
        $print = $invData['Print'] ?? 1;
        $carruerType = $invData['CarruerType'] ?? '';
        $donation = $invData['Donation'] ?? 2;
        $invType = $invData['InvType'] ?? '07';
        $customerIdentifier = $invData['CustomerIdentifier'] ?? '';
        $customerName = StringService::replaceSymbol(urlencode($invData['CustomerName'] ?? ''));
        $customerAddress = StringService::replaceSymbol(urlencode($invData['CustomerAddr'] ?? ''));
        $customEmail = StringService::replaceSymbol(urlencode($invData['CustomerEmail'] ?? ''));
        $customPhone = $invData['CustomerPhone'] ?? '';

        $this->postData->put('TimeStamp', Carbon::now()->timestamp);
        $this->postData->put('MerchantID', $this->merchantId);
        $this->postData->put('RelateNumber', $this->postData->get('MerchantTradeNo')??
                                            StringService::identifyNumberGenerator('O'));
        $this->postData->put('TaxType', (string)$taxType);
        $this->postData->put('SalesAmount', $amount);
        $this->postData->put('ItemName', StringService::replaceSymbol(urlencode((string)$itemNameInvoice)));
        $this->postData->put('ItemCount', (string)$itemCountInvoice);
        $this->postData->put('ItemWord', StringService::replaceSymbol(urlencode((string)$itemUnitInvoice)));
        $this->postData->put('ItemPrice', (string)$itemPriceInvoice);
        $this->postData->put('ItemAmount', (string)$itemAmountInvoice);
        $this->postData->put('InvType', (string)$invType);
        $this->postData->put('Print', (string)$print);

        $this->postData->put('CustomerID', (string)$invData['UserId']);
        $this->postData->put('CustomerIdentifier', $customerIdentifier);
        if ($print == 0) {
            if (empty($carruerType) && empty($donation)) {
                $validator->getMessageBag()
                    ->add('CarruerType and Donation', 'CarruerType or Donation must be required while Print is 0');
            }
            $this->postData->put('CarruerType', (string)$carruerType);
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
            $donation = 2;
        }
        if (strlen($customerName) > 60) {
            $validator->getMessageBag()->add('CustomerName', 'CustomerName can not great then 60 characters');
        }
        $this->postData->put('CustomerName', $customerName);

//        if (strlen($customerAddress) > 200) {
//            $validator->getMessageBag()->add('CustomerAddr', 'CustomerAddr can not great then 200 characters');
//        }
//        $this->postData->put('CustomerAddr', $customerAddress);
        $this->postData->put('CustomerPhone', $customPhone);

//        if (strlen($customEmail) > 200) {
//            $validator->getMessageBag()->add('CustomerEmail', 'CustomerEmail can not great then 200 characters');
//        }
//        $this->postData->put('CustomerEmail', $customEmail);
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
        $this->postData->put('Donation', (string)$donation);
        if ($donation == 1) {
            $this->postData->put('LoveCode', (string)$invData['LoveCode']);
        }
        if ( $validator->getMessageBag()->count() > 0 ) {
            throw new ECPayException($validator->getMessageBag());
        }
        return $this;
    }
}