<?php
namespace TsaiYiHua\ECPay\Collections;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use TsaiYiHua\ECPay\Exceptions\ECPayException;
use TsaiYiHua\ECPay\Services\StringService;
use TsaiYiHua\ECPay\Validations\InvoiceValidation;

class InvoicePostCollection extends Collection
{
    public $merchantId;
    public $attributes;
    public $notifyUrl;

    public function __construct()
    {
        parent::__construct();
        $this->merchantId = config('ecpay.MerchantId');
        $this->notifyUrl = route('ecpay.notify');
    }

    public function setData($attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }

    public function setBasicInfo()
    {
        $this->put('TimeStamp', Carbon::now()->timestamp);
        $this->put('MerchantID', $this->merchantId);
        return $this;
    }

    /**
     * @return $this
     * @throws ECPayException
     */
    public function setPostData()
    {
        $validator = InvoiceValidation::invoiceValidator($this->attributes);
        $items = $this->attributes['Items'];
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

        $taxType = $this->attributes['TaxType'] ?? 1;
        $print = $this->attributes['Print'] ?? 1;
        $carruerType = $this->attributes['CarruerType'] ?? '';
        $donation = $this->attributes['Donation'] ?? 2;
        $invType = $this->attributes['InvType'] ?? '07';
        $customerIdentifier = $this->attributes['CustomerIdentifier'] ?? '';
        $customerName = StringService::replaceSymbol(urlencode($this->attributes['CustomerName'] ?? ''));
        $customerAddress = StringService::replaceSymbol(urlencode($this->attributes['CustomerAddr'] ?? ''));
        $customEmail = StringService::replaceSymbol(urlencode($this->attributes['CustomerEmail'] ?? ''));
        $customPhone = $this->attributes['CustomerPhone'] ?? '';

        $this->put('RelateNumber', $this->attributes['MerchantTradeNo']??
            StringService::identifyNumberGenerator('O'));
        $this->put('TaxType', (string)$taxType);
        $this->put('SalesAmount', $amount);
        $this->put('ItemName', StringService::replaceSymbol(urlencode((string)$itemNameInvoice)));
        $this->put('ItemCount', (string)$itemCountInvoice);
        $this->put('ItemWord', StringService::replaceSymbol(urlencode((string)$itemUnitInvoice)));
        $this->put('ItemPrice', (string)$itemPriceInvoice);
        $this->put('ItemAmount', (string)$itemAmountInvoice);
        $this->put('InvType', (string)$invType);
        $this->put('Print', (string)$print);

        $this->put('CustomerID', (string)$this->attributes['UserId']);
        $this->put('CustomerIdentifier', $customerIdentifier);
        if ($print == 0) {
            if (empty($carruerType) && empty($donation)) {
                $validator->getMessageBag()
                    ->add('CarruerType and Donation', 'CarruerType or Donation must be required while Print is 0');
            }
            $this->put('CarruerType', (string)$carruerType);
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
        $this->put('CustomerName', $customerName);

        if (strlen($customerAddress) > 200) {
            $validator->getMessageBag()->add('CustomerAddr', 'CustomerAddr can not great then 200 characters');
        }
        $this->put('CustomerAddr', $customerAddress);
        $this->put('CustomerPhone', $customPhone);

        if (strlen($customEmail) > 200) {
            $validator->getMessageBag()->add('CustomerEmail', 'CustomerEmail can not great then 200 characters');
        }
        $this->put('CustomerEmail', $customEmail);

        if ($taxType == '2') {
            $okValue = [1,2];
            $clearanceMark = $this->attributes['ClearanceMark'] ?? '';
            if (in_array($clearanceMark, $okValue)) {
                $this->put('ClearanceMark', $clearanceMark);
            } else {
                $validator->getMessageBag()
                    ->add('ClearanceMark', 'ClearanceMark must be 1 or 2 while TaxType is 2');
            }
        }
        if ($carruerType == 2 || $carruerType == 3) {
            $this->put('CarruerNum', $this->attributes['CarruerNum']);
        } else {
            $this->put('CarruerNum', '');
        }
        $this->put('Donation', (string)$donation);
        if ($donation == 1) {
            $this->put('LoveCode', (string)$this->attributes['LoveCode']);
        }
        if ( $validator->getMessageBag()->count() > 0 ) {
            throw new ECPayException($validator->getMessageBag());
        }
        return $this;
    }

    /**
     * @return $this
     * @throws ECPayException
     */
    public function setPostDataForCheckout()
    {
        $validator = InvoiceValidation::invoiceValidator($this->attributes);
        $items = $this->attributes['Items'];
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

        $taxType = $this->attributes['TaxType'] ?? 1;
        $delayDay = $this->attributes['DelayDay'] ?? 0;
        $print = $this->attributes['Print'] ?? 1;
        $carruerType = $this->attributes['CarruerType'] ?? '';
        $donation = $this->attributes['Donation'] ?? '';
        $invType = $this->attributes['InvType'] ?? '07';
        $customerIdentifier = $this->attributes['CustomerIdentifier'] ?? '';
        $customerName = StringService::replaceSymbol(urlencode($this->attributes['CustomerName'] ?? ''));
        $customerAddress = StringService::replaceSymbol(urlencode($this->attributes['CustomerAddr'] ?? ''));
        $customEmail = StringService::replaceSymbol(urlencode($this->attributes['CustomerEmail'] ?? ''));
        $customPhone = $this->attributes['CustomerPhone'] ?? '';

        $this->put('InvoiceMark', 'Y');
        $this->put('RelateNumber', $this->attributes['MerchantTradeNo']??StringService::identifyNumberGenerator('O'));
        $this->put('TaxType', (string)$taxType);
        $this->put('InvoiceItemName', $itemNameInvoice);
        $this->put('InvoiceItemCount', $itemCountInvoice);
        $this->put('InvoiceItemWord', $itemUnitInvoice);
        $this->put('InvoiceItemPrice', $itemPriceInvoice);
        $this->put('DelayDay', $delayDay);
        $this->put('InvType', $invType);
        $this->put('Print', (string)$print);

        $this->put('CustomerID', (string)$this->attributes['UserId']);
        $this->put('CustomerIdentifier', $customerIdentifier);
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
        $this->put('CustomerName', $customerName);

        if (strlen($customerAddress) > 200) {
            $validator->getMessageBag()->add('CustomerAddr', 'CustomerAddr can not great then 200 characters');
        }
        $this->put('CustomerAddr', $customerAddress);
        $this->put('CustomerPhone', $customPhone);

        if (strlen($customEmail) > 200) {
            $validator->getMessageBag()->add('CustomerEmail', 'CustomerEmail can not great then 200 characters');
        }
        $this->put('CustomerEmail', $customEmail);
        if ($taxType == '2') {
            $okValue = [1,2];
            $clearanceMark = $this->attributes['ClearanceMark'] ?? '';
            if (in_array($clearanceMark, $okValue)) {
                $this->put('ClearanceMark', $clearanceMark);
            } else {
                $validator->getMessageBag()
                    ->add('ClearanceMark', 'ClearanceMark must be 1 or 2 while TaxType is 2');
            }
        }
        if ($carruerType == 2 || $carruerType == 3) {
            $this->put('CarruerNum', $this->attributes['CarruerNum']);
        } else {
            $this->put('CarruerNum', '');
        }
        $this->put('Donation', $donation);
        if ($donation == 1) {
            $this->put('LoveCode', $this->attributes['LoveCode']);
        }
        if ( $validator->getMessageBag()->count() > 0 ) {
            throw new ECPayException($validator->getMessageBag());
        }
        return $this;
    }
}