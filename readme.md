# Laravel ECPay
Laravel ECPay 為串接綠界的非官方套件

## 系統需求
 - PHP >= 7
 - Laravel >= 5.7

## 安裝
```composer require tsaiyihua/laravel-ecpay```

## 環境設定
```php artisan vendor:publish --tag=ecpay```  
### .env 裡加入
```
ECPAY_MERCHANT_ID=
ECPAY_HASH_KEY=
ECPAY_HASH_IV=
ECPAY_INVOICE_HASH_KEY=
ECPAY_INVOICE_HASH_IV=
```
 - 金流測試用的參數值請參考介接文件 ecpay_gw_p110.pdf 第11頁。
 - 查詢發票用的參數請請參考介接文件 ecpay_004.pdf 第6頁。

## 用法
### 基本用法
  - 產品資料單筆時可簡單只傳送 ItemName 及 TotalAmount
```php
...
    public function __construct(Checkout $checkout)
    {
        $this->checkout = $checkout;
    }
...

    public function sendOrder()
    {
        $formData = [
            'ItemDescription' => '產品簡介',
            'ItemName' => 'Product Name',
            'TotalAmount' => '2000',
            'PaymentMethod' => 'Credit', // ALL, Credit, ATM, WebATM
        ];
        return $this->checkout->setPostData($formData)->send();
    }
```
### 需要分期付款時
 - 加上 withInstallment(分期期數)
 - 信用卡分期可用參數為:3,6,12,18,24
 - ex： 3,6
 範例  
   承上，在 return 時，加上 withInstallment 即可
```php
    return $this->checkout->setPostData($formData)->withInstallment('3,6')->send();
```
### 定期定額扣款
 - 加上 withPeriodAmount($periodAmt)
##### 範例  
    承上，加上參數，帶入 withPeriodAmount 即可
```php
...
    $periodAmt = [
        'PeriodAmount' => 2550,
        'PeriodType' => 'M',
        'Frequency' => '1',
        'ExecTimes' => 10,
        'PeriodReturnURL'
    ];
    return $this->checkout->setPostData($formData)->withPeriodAmount($periodAmt)->send();
```
### 需要開立發票時
 - 加上 withInvoice($invData) 即可。
 - 開立發票時，產品內容必須要符合即定格式傳送，不能只帶 ItemName 及 TotalAmount
 - 開立發票時，特店必須要有會員系統並傳送會員相關資料
 - 測試開立發票時，MerchantID 請設 2000132
##### 範例  
```php
...
    public function __construct(Checkout $checkout)
    {
        $this->checkout = $checkout;
    }
...

    public function sendOrder()
    {
        $items[0] = [
            'name' => '產品333',
            'qty' => '3',
            'unit' => '個',
            'price' => '150'
        ];
        $formData = [
            'itemDescription' => '產品簡介',
            'items' => $items,
            'paymentMethod' => 'Credit',
            'userId' => 1
        ];
        $invData = [
            'items' => $items,
            'userId' => 1,
            'CustomerName' => 'User Name',
            'CustomerAddr' => 'ABC 123',
            'CustomerEmail' => 'email@address'
        ];
        return $this->checkout->setPostData($formData)->withInvoice($invData)->send();
    }
```
### 查詢訂單
```php
    public function __construct(Checkout $checkout,  QueryTradeInfo $queryTradeInfo)
    {
        $this->checkout = $checkout;
        $this->queryTradeInfo = $queryTradeInfo;
    }
    ...
    public function queryInfo()
    {
        $formData = ['orderId'=>'O154320382117474878'];
        return $this->queryTradeInfo->getData($formData)->query();
    }
```
### 查詢發票
```php
    public function __construct(Checkout $checkout,  QueryInvoice $queryInvoice)
    {
        $this->checkout = $checkout;
        $this->queryInvoice = $queryInvoice;
    }
    ...
    public function queryInvInfo()
    {
        $formData = ['orderId'=>'O154320382117474878'];
        return $this->queryInvoice->getData($formData)->query();
    }
```

### 所有文件列的參數基本上都可用，參數用法請參考綠界串接文件
 - 回傳參數的背景通知(ReturnURL)，套件裡有預設的網址，但只止於通知，如果要有寫入資料庫的設計，要再設計自己的回傳通知網址。
 - OrderResultURL 為結帳完返回自已站台的網址，不能與 ReturnURL 相同。

## 參考文件
 - 綠界科技全方位金流信用卡介接技術文件 (2018-10-08)
   - V 5.1.21
   - 文件編號 gw_p110
   - 文件位置 documents/ecpay_gw_p110.pdf