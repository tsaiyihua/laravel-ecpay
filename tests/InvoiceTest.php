<?php
namespace TsaiYiHua\ECPay\Tests;

use TsaiYiHua\ECPay\Collections\InvoiceResponseCollection;
use TsaiYiHua\ECPay\Constants\ECPayDonation;
use TsaiYiHua\ECPay\Exceptions\ECPayException;
use TsaiYiHua\ECPay\Invoice;
use TsaiYiHua\ECPay\Services\StringService;

//uses(TestCase::class)->in('.');

test('issue invoice', function() {
    $invoice = $this->app->make(Invoice::class);
    $itemData[] = [
        'name' => 'Product '.random_int(1,50),
        'qty' => 1,
        'unit' => 'piece',
        'price' => random_int(1000,5000)
    ];
    $invData = [
        'UserId' => 1,
        'Items' => $itemData,
        'CustomerName' => 'User Name',
        'CustomerEmail' => 'email@address.com',
        'CustomerPhone' => '0912345678',
        'OrderId' => StringService::identifyNumberGenerator('O'),
        'Donation' => ECPayDonation::Yes,
        'LoveCode' => 168001,
        'Print' => 0,
        'CarruerType' => 1
    ];
    $invoiceData = $invoice->setPostData($invData)->send();
    $this->assertEquals("1", $invoiceData['RtnCode']);

    /** @var InvoiceResponseCollection $invoiceResponse */
    $invoiceResponse = $this->app->make(InvoiceResponseCollection::class);
    $invoiceResponse->collectResponse($invoiceData);
    $this->assertEquals("1", $invoiceResponse->getStatus());
});

test('invoice response collection - fail', function() {
    $response = [
    ];
    /** @var InvoiceResponseCollection $invoiceResponse */
    $invoiceResponse = $this->app->make(InvoiceResponseCollection::class);
    $invoiceResponse->collectResponse($response);
})->throws(ECPayException::class);
