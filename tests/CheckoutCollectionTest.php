<?php
namespace TsaiYiHua\ECPay\Tests;

use Illuminate\Support\Facades\Config;
use TsaiYiHua\ECPay\Collections\CheckoutPostCollection;
use TsaiYiHua\ECPay\Collections\CheckoutResponseCollection;
use TsaiYiHua\ECPay\Exceptions\ECPayException;
use TsaiYiHua\ECPay\Services\StringService;

uses(TestCase::class)->in('.');

test('set checkout post collection - setBasicInfo fail', function() {
    $checkoutPostCollection = new CheckoutPostCollection();
    $checkoutPostCollection->setBasicInfo();
})->throws(ECPayException::class);

test('set checkout post collection - setOrderInfo fail', function() {
    $checkoutPostCollection = new CheckoutPostCollection();
    $checkoutPostCollection->setOrderInfo();
})->throws(ECPayException::class);

test('set checkout post collection without item data - setOrderInfo fail', function() {
    $orderId = StringService::identifyNumberGenerator('O');
    $this->formData = [
        'OrderId' => $orderId,
        'UserId' => 1, // 用戶ID , Optional
        'ItemDescription' => '產品簡介',
        'PaymentMethod' => 'Credit', // ALL, Credit, ATM, WebATM
        'Language' => 'EN'
    ];
    $checkoutPostCollection = new CheckoutPostCollection();
    $checkoutPostCollection->setData($this->formData)->setOrderInfo();
})->throws(ECPayException::class);

test('set checkout post collection for multi items - setOrderInfo', function() {
    $orderId = StringService::identifyNumberGenerator('O');
    $itemData = [
        [
            'name' => 'Product Name 1',
            'qty' => 1,
            'unit' => 'piece',
            'price' => 2000
        ],
        [
            'name' => 'Product Name 2',
            'qty' => 1,
            'unit' => 'piece',
            'price' => 200
        ],
    ];
    $this->formData = [
        'OrderId' => $orderId,
        'UserId' => 1, // 用戶ID , Optional
        'ItemDescription' => '產品簡介',
        'PaymentMethod' => 'Credit', // ALL, Credit, ATM, WebATM
        'Language' => 'EN',
        'Items' => $itemData
    ];
    $checkoutPostCollection = new CheckoutPostCollection();
    $checkoutPostCollection->setData($this->formData)->setOrderInfo();
    $this->assertEquals($orderId, $checkoutPostCollection->attributes['OrderId']);
});

test('set checkout post collection too many characters - setOrderInfo fail', function() {
    $orderId = StringService::identifyNumberGenerator('O');
    $itemData = [
        [
        'name' => 'Product Name 1000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000',
        'qty' => 1,
        'unit' => 'piece',
        'price' => 2000
        ],
        [
            'name' => 'Product Name 2000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000',
            'qty' => 1,
            'unit' => 'piece',
            'price' => 200
        ],
    ];
    $this->formData = [
        'OrderId' => $orderId,
        'UserId' => 1, // 用戶ID , Optional
        'ItemDescription' => '產品簡介',
        'PaymentMethod' => 'Credit', // ALL, Credit, ATM, WebATM
        'Language' => 'EN',
        'Items' => $itemData
    ];
    $checkoutPostCollection = new CheckoutPostCollection();
    $checkoutPostCollection->setData($this->formData)->setOrderInfo();
})->throws(ECPayException::class);

test('set checkout post collection invalid data for Items - setOrderInfo fail', function() {
    $orderId = StringService::identifyNumberGenerator('O');
    $itemData = [
        [
            'name' => 'Product Name 1',
            'qty' => 2,
            'unit' => 'piece piece',
            'price' => 2000
        ],
        [
            'name' => 'Product Name 2',
            'qty' => 1,
            'unit' => 'piece',
            'price' => "200 USD"
        ],
    ];
    $this->formData = [
        'OrderId' => $orderId,
        'UserId' => 1, // 用戶ID , Optional
        'ItemDescription' => '產品簡介',
        'PaymentMethod' => 'Credit', // ALL, Credit, ATM, WebATM
        'Language' => 'EN',
        'Items' => $itemData
    ];
    $checkoutPostCollection = new CheckoutPostCollection();
    $checkoutPostCollection->setData($this->formData)->setOrderInfo();
})->throws(ECPayException::class);

test('set checkout post collection - setOrderInfo', function() {
    $orderId = StringService::identifyNumberGenerator('O');
    $this->formData = [
        'OrderId' => $orderId,
        'UserId' => 1, // 用戶ID , Optional
        'ItemName' => 'Product Name',
        'TotalAmount' => '2000',
        'ItemDescription' => '產品簡介',
        'PaymentMethod' => 'Credit', // ALL, Credit, ATM, WebATM
        'Language' => 'EN'
    ];
    $checkoutPostCollection = new CheckoutPostCollection();
    $checkoutPostCollection->setData($this->formData)->setOrderInfo();
    $this->assertEquals($orderId, $checkoutPostCollection->attributes['OrderId']);
});

test('set checkout post collection - setOptional fail', function() {
    $checkoutPostCollection = new CheckoutPostCollection();
    $checkoutPostCollection->setOptionalInfo();
})->throws(ECPayException::class);

test('set checkout post collection - optimize fail', function() {
    $checkoutPostCollection = new CheckoutPostCollection();
    $checkoutPostCollection->optimize();
})->throws(ECPayException::class);

test('set checkout post collection - optimize', function() {
    $orderId = StringService::identifyNumberGenerator('O');
    $this->formData = [
        'OrderId' => $orderId,
        'UserId' => 1, // 用戶ID , Optional
        'ItemName' => 'Product Name',
        'TotalAmount' => '2000',
        'ItemDescription' => '產品簡介',
        'PaymentMethod' => 'Credit', // ALL, Credit, ATM, WebATM
        'Language' => 'EN',
        'CreditInstallment' => '3,6',
        'PlatformID' => 'MerchantID'
    ];
    $checkoutPostCollection = new CheckoutPostCollection();
    $checkoutPostCollection->setData($this->formData)->setOrderInfo()->optimize();
    $this->assertEquals($orderId, $checkoutPostCollection->attributes['OrderId']);
});

test('set checkout post collection - optimize 2', function() {
    $orderId = StringService::identifyNumberGenerator('O');
    $this->formData = [
        'OrderId' => $orderId,
        'UserId' => 1, // 用戶ID , Optional
        'ItemName' => 'Product Name',
        'TotalAmount' => '2000',
        'ItemDescription' => '產品簡介',
        'PaymentMethod' => 'Credit', // ALL, Credit, ATM, WebATM
        'Language' => 'EN',
        'Redeem' => 'Y'
    ];
    $checkoutPostCollection = new CheckoutPostCollection();
    $checkoutPostCollection->setData($this->formData)->setOrderInfo()->optimize();
    $this->assertEquals($orderId, $checkoutPostCollection->attributes['OrderId']);
});

test('set checkout post collection - optimize 3', function() {
    Config::set('app.env', 'production');
    $orderId = StringService::identifyNumberGenerator('O');
    $this->formData = [
        'OrderId' => $orderId,
        'UserId' => 1, // 用戶ID , Optional
        'ItemName' => 'Product Name',
        'TotalAmount' => '2000',
        'ItemDescription' => '產品簡介',
        'PaymentMethod' => 'Credit', // ALL, Credit, ATM, WebATM
        'Language' => 'EN',
        'Redeem' => 'Y'
    ];
    $checkoutPostCollection = new CheckoutPostCollection();
    $checkoutPostCollection->setData($this->formData)->setOrderInfo()->optimize();
    $this->assertEquals($orderId, $checkoutPostCollection->attributes['OrderId']);
});

test('set checkout post collection - setPeriodAmount', function() {
    $orderId = StringService::identifyNumberGenerator('O');
    $this->formData = [
        'OrderId' => $orderId,
        'UserId' => 1, // 用戶ID , Optional
        'ItemName' => 'Product Name',
        'TotalAmount' => '200',
        'ItemDescription' => '產品簡介',
        'PaymentMethod' => 'Credit', // ALL, Credit, ATM, WebATM
        'Language' => 'EN',
        'Redeem' => 'Y'
    ];
    $periodAmountData = [
        'PeriodAmount' => 200,
        'PeriodType' => 'M',
        'Frequency' => '1',
        'ExecTimes' => 12,
        'PeriodReturnURL'
    ];
    $checkoutPostCollection = new CheckoutPostCollection();
    $checkoutPostCollection->setData($this->formData)->setOrderInfo()->setPeriodAmount($periodAmountData);
    $this->assertEquals($orderId, $checkoutPostCollection->attributes['OrderId']);
});

test('set checkout post collection - setPeriodAmount fail', function() {
    $orderId = StringService::identifyNumberGenerator('O');
    $this->formData = [
        'OrderId' => $orderId,
        'UserId' => 1, // 用戶ID , Optional
        'ItemName' => 'Product Name',
        'TotalAmount' => '2000',
        'ItemDescription' => '產品簡介',
        'PaymentMethod' => 'Credit', // ALL, Credit, ATM, WebATM
        'Language' => 'EN',
        'Redeem' => 'Y'
    ];
    $periodAmountData = [
        'PeriodAmount' => 200,
        'PeriodType' => 'M',
        'Frequency' => '1',
        'ExecTimes' => 12,
        'PeriodReturnURL'
    ];
    $checkoutPostCollection = new CheckoutPostCollection();
    $checkoutPostCollection->setData($this->formData)->setOrderInfo()->setPeriodAmount($periodAmountData);
})->throws(ECPayException::class);

test('checkout response collection - fail', function() {
    $orderId = StringService::identifyNumberGenerator('O');
    $postData = [
        'RtnMsg' => '交易成功'
    ];
    $checkoutResponseCollection = new CheckoutResponseCollection();
    $checkoutResponseCollection->collectResponse($postData);
})->throws(ECPayException::class);

test('checkout response collection', function() {
    $orderId = StringService::identifyNumberGenerator('O');
    $postData = [
        'RtnCode' => 1, // success,
        'RtnMsg' => '交易成功',
        'MerchantID' => config('ecpay.MerchantId'),
        'MerchantTradeNo' => $orderId,
        'StoreID' => 'store123',
        'TradeNo' => '20120315174058256424',
        'TradeAmt' => 2000,
        'PaymentDate' => '2012/03/16 12:03:12',
        'PaymentType' => 'Credit_CreditCard',
        'TradeDate' => '2012/03/15 17:40:58',
        'SimulatePaid' => 1,
        'CustomField1' => '',
        'CustomField2' => '',
        'CustomField3' => '',
        'CustomField4' => '',
        'CheckMacValue' => '9139AF2AC5D0F9EBC5F3CD44064F666AAA62F0B202B95B341CC25E080EA4FC6E'
    ];
    $checkoutResponseCollection = new CheckoutResponseCollection();
    $checkoutResponseCollection->collectResponse($postData);
    $this->assertEquals(1, $checkoutResponseCollection->getStatus());
    $this->assertEquals('交易成功', $checkoutResponseCollection->getMessage());
});

