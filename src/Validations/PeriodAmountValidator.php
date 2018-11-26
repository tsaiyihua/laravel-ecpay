<?php
/**
 * Created by PhpStorm.
 * User: yihua
 * Date: 2018/11/26
 * Time: 下午 1:46
 */

namespace TsaiYiHua\ECPay\Validations;


use Illuminate\Support\Facades\Validator;

class PeriodAmountValidator
{
    static public function periodAmtValidator($data)
    {
        $validator = Validator::make($data, [
            'PeriodAmount' => 'required|int',
            'PeriodType' => 'required|max:1|in:D,M,Y',
            'Frequency' => ['required','int', 'min:1', function ($attribute, $value, $fail) use($data){
                if ($data['PeriodType'] == 'D' && $value > 365) {
                    $fail($attribute.' maximum value is 365 while PeriodType is D');
                }
                if ($data['PeriodType'] == 'M' && $value > 12) {
                    $fail($attribute.' maximum value is 12 while PeriodType is M');
                }
                if ($data['PeriodType'] == 'Y' && $value > 1) {
                    $fail($attribute.' maximum value is 1 while PeriodType is Y');
                }
            }],
            'ExecTimes' => ['required','int', 'min:1', function ($attribute, $value, $fail) use($data){
                if ($data['PeriodType'] == 'D' && $value > 999) {
                    $fail($attribute.' maximum value is 999 while PeriodType is D');
                }
                if ($data['PeriodType'] == 'M' && $value > 99) {
                    $fail($attribute.' maximum value is 99 while PeriodType is M');
                }
                if ($data['PeriodType'] == 'Y' && $value > 9) {
                    $fail($attribute.' maximum value is 9 while PeriodType is Y');
                }
            }],
            'PeriodReturnURL' => 'url|max:200'
        ]);
        return $validator;
    }
}