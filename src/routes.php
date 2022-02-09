<?php
use TsaiYiHua\ECPay\Http\Controllers\ECPayController;

Route::prefix('ecpay')->group(function(){
    Route::post('notify', [ECPayController::class, 'notifyUrl'])->name('ecpay.notify');
    Route::post('return',  [ECPayController::class, 'returnUrl'])->name('ecpay.return');
});
