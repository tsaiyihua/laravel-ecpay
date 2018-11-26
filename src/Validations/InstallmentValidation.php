<?php
/**
 * Created by PhpStorm.
 * User: yihua
 * Date: 2018/11/26
 * Time: 下午 1:24
 */

namespace TsaiYiHua\ECPay\Validations;


use Illuminate\Support\Facades\Validator;

class InstallmentValidation
{
    static public function installmentValidator($data)
    {
        $availableInstallment = ['3', '6', '12', '18', '24'];
        $validator = Validator::make(['CreditInstallment'=>$data], [
            'CreditInstallment' => ['required',function ($attribute, $value, $fail) use ($availableInstallment) {
                $buf = explode(',', $value);
                $ok = true;
                for($i=0; $i<sizeof($buf); $i++) {
                    if (!in_array($buf[$i], $availableInstallment)) {
                        $ok = false;
                    }
                }
                if ($ok === false) {
                    $fail($attribute.' value must be composed by 3, 6, 12, 18, 24');
                }
            }]
        ]);
        return $validator;
    }
}