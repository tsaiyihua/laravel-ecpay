<?php
namespace TsaiYiHua\ECPay\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use TsaiYiHua\ECPay\Services\StringService;

class ECPayController extends Controller
{
    public function notifyUrl(Request $request)
    {
        $serverPost = $request->post();
        $checkMacValue = $request->post('CheckMacValue');
        unset($serverPost['CheckMacValue']);
        $checkCode = StringService::checkMacValueGenerator($serverPost);
        if ($checkMacValue == $checkCode) {
            return '1|OK';
        } else {
            return '0|FAIL';
        }
    }
}