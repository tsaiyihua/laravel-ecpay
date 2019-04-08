<?php
return [
    'MerchantId' => env('ECPAY_MERCHANT_ID', ''),
    'HashKey' => env('ECPAY_HASH_KEY', ''),
    'HashIV' => env('ECPAY_HASH_IV', ''),
    'InvoiceHashKey' => env('ECPAY_INVOICE_HASH_KEY', ''),
    'InvoiceHashIV' => env('ECPAY_INVOICE_HASH_IV', ''),
    'SendForm' => env('ECPAY_SEND_FORM', null)
];