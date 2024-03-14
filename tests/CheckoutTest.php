<?php
namespace TsaiYiHua\ECPay\Tests;

use Illuminate\Support\Facades\Config;
use Illuminate\View\View;
use TsaiYiHua\ECPay\Checkout;
use TsaiYiHua\ECPay\Collections\CheckoutPostCollection;
use TsaiYiHua\ECPay\Constants\ECPayCarruerType;
use TsaiYiHua\ECPay\Constants\ECPayDonation;
use TsaiYiHua\ECPay\ECPay;
use TsaiYiHua\ECPay\Exceptions\ECPayException;
use TsaiYiHua\ECPay\Services\StringService;

//uses(TestCase::class)->in('.');

beforeEach(function () {
    /** @var TestCase $this */
    $this->checkout = $this->app->make(Checkout::class);
    $orderId = StringService::identifyNumberGenerator('O');
    $this->formData = [
        'OrderId' => $orderId,
        'UserId' => 1, // 用戶ID , Optional
        'ItemDescription' => '產品簡介',
        'ItemName' => 'Product Name',
        'TotalAmount' => '2000',
        'PaymentMethod' => 'Credit', // ALL, Credit, ATM, WebATM
        'Language' => 'EN'
    ];
    $this->periodAmountData = [
        'PeriodAmount' => '2000',
        'PeriodType' => 'M', // D, Y, M
        'Frequency' => 1,
        'ExecTimes' => 12,
        'PeriodReturnURL' => 'https://localhost/period-return-url'
    ];
});

test('api url for production', function() {
    /** @var CheckoutPostCollection $postData */
    Config::set('app.env', 'production');
    $this->checkout = $this->app->make(Checkout::class);
    $this->assertTrue(true);
});

test('set post data success', function() {
    /** @var CheckoutPostCollection $postData */
    $postData = $this->checkout->setPostData($this->formData)->getPostData();
    $this->assertEquals($this->formData['OrderId'], $postData->get('MerchantTradeNo'));
});

test('set post data fail', function() {
    $this->formData = [
        'PaymentMethod' => 'MyPay', // ALL, Credit, ATM, WebATM
    ];
    $this->checkout->setPostData($this->formData)->getPostData();
})->throws(ECPayException::class);

test('set post data with platform', function() {
    $platformId = 'A123456789';
    /** @var CheckoutPostCollection $postData */
    $postData = $this->checkout->setPostData($this->formData)->setPlatform($platformId)->getPostData();
    $this->assertEquals($platformId, $postData->get('PlatformId'));
});

test('set post data with invalid platform id', function() {
    $platformId = 'A123456789f21341gs213';
    /** @var CheckoutPostCollection $postData */
    $this->checkout->setPostData($this->formData)->setPlatform($platformId)->getPostData();
})->throws(ECPayException::class);

test('set post data with installment', function() {
    /** @var CheckoutPostCollection $postData */
    $postData = $this->checkout->setPostData($this->formData)->withInstallment(3)->getPostData();
    $this->assertEquals(3, $postData->get('CreditInstallment'));
});

test('set post data with invalid installment data', function() {
    /** @var CheckoutPostCollection $postData */
    $postData = $this->checkout->setPostData($this->formData)->withInstallment(5)->getPostData();
    $this->assertEquals(3, $postData->get('CreditInstallment'));
})->throws(ECPayException::class);

test('set post data with Period Amount', function() {
    /** @var CheckoutPostCollection $postData */
    $postData = $this->checkout->setPostData($this->formData)->withPeriodAmount($this->periodAmountData)->getPostData();
    $this->assertEquals(12, $postData->get('ExecTimes'));
});

test('set post data with Period Amount has Invalid Data', function() {
    $this->periodAmountData['PeriodType'] = 'N';
    /** @var CheckoutPostCollection $postData */
    $postData = $this->checkout->setPostData($this->formData)->withPeriodAmount($this->periodAmountData)->getPostData();
    $this->assertEquals(12, $postData->get('ExecTimes'));
})->throws(ECPayException::class);

test('set post data with invoice', function() {
    $this->itemData[] = [
        'name' => 'Product Name',
        'qty' => 1,
        'unit' => 'piece',
        'price' => 2000
    ];
    $this->invoiceData = [
        'UserId' => 1,
        'Items' => $this->itemData,
        'CustomerName' => 'User Name',
//            'CustomerIdentifier' => '12345868',
        'CustomerEmail' => 'email@address.com',
        'CustomerPhone' => '0912345678',
        'OrderId' => $this->formData['OrderId'],
        'Donation' => ECPayDonation::Yes,
        'LoveCode' => 168001,
        'Print' => 0,
        'CarruerType' => ECPayCarruerType::None
    ];
    $postData = $this->checkout->setPostData($this->formData)->withInvoice($this->invoiceData)->getPostData();
    $this->assertEquals(ECPayCarruerType::None, $postData->get('CarruerType'));
});

test('set send post data', function() {
    /** @var View $view */
    $view = $this->checkout->setPostData($this->formData)->send();
    $this->assertInstanceOf(View::class, $view);
});

test('set send post data with specified view via config', function() {
    Config::set('ecpay.SendForm', 'ecpay::send');
    /** @var View $view */
    $view = $this->checkout->setPostData($this->formData)->send();
    $this->assertInstanceOf(View::class, $view);
});

test('set send post data with specified view', function() {
    ECPay::$sendForm = 'ecpay::send';
    /** @var View $view */
    $view = $this->checkout->setPostData($this->formData)->send();
    $this->assertInstanceOf(View::class, $view);
});

test('set notify url', function() {
    $notifyUrl = 'https://localhost/notify';
    $this->checkout->setNotifyUrl($notifyUrl);
    $this->assertEquals($notifyUrl, $this->checkout->getPostData()->notifyUrl);
});

test('set return url', function() {
    $returnUrl = 'https://localhost/return';
    $this->checkout->setReturnUrl($returnUrl);
    $this->assertEquals($returnUrl, $this->checkout->getPostData()->returnUrl);
});
