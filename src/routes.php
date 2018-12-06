<?php

Route::prefix('ecpay')->group(function(){
    Route::post('notify', 'TsaiYiHua\ECPay\Http\Controllers\ECPayController@notifyUrl')
        ->name('ecpay.notify');
    Route::post('return', 'TsaiYiHua\ECPay\Http\Controllers\ECPayController@returnUrl')
        ->name('ecpay.return');
});