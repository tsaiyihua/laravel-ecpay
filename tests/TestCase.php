<?php
namespace TsaiYiHua\ECPay\Tests;

use TsaiYiHua\ECPay\Checkout;
use TsaiYiHua\ECPay\ECPayServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected Checkout $checkout;
    protected array $formData;
    protected array $itemData;
    protected array $periodAmountData;
    protected array $invoiceData;
    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @param $app
     * @return string[]
     */
    protected function getPackageProviders($app)
    {
        return [
            ECPayServiceProvider::class
        ];
    }

    /**
     * @param $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);
        $app['config']->set('ecpay', [
            'MerchantId' => env('ECPAY_MERCHANT_ID', '2000132'),
            'HashKey' => env('ECPAY_HASH_KEY', '5294y06JbISpM5x9'),
            'HashIV' => env('ECPAY_HASH_IV', 'v77hoKGq4kWxNNIS'),
            'InvoiceHashKey' => env('ECPAY_INVOICE_HASH_KEY', 'ejCk326UnaZWKisg'),
            'InvoiceHashIV' => env('ECPAY_INVOICE_HASH_IV', 'q9jcZX8Ib9LM8wYk'),
            'SendForm' => env('ECPAY_SEND_FORM', null)
        ]);
        $app['config']->set('app.debug', true);
    }

    /**
     * @param $method
     * @param $content
     * @param $uri
     * @param $server
     * @param $parameters
     * @param $cookies
     * @param $files
     * @return \Illuminate\Http\Request
     */
    public function createRequest(
        $method,
        $content,
        $uri = '/test',
        $server = ['CONTENT_TYPE' => 'application/json'],
        $parameters = [],
        $cookies = [],
        $files = []
    ) {
        $request = new \Illuminate\Http\Request;
        return $request->createFromBase(
            \Symfony\Component\HttpFoundation\Request::create(
                $uri,
                $method,
                $parameters,
                $cookies,
                $files,
                $server,
                $content
            )
        );
    }
}
