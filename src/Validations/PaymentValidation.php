<?php
namespace TsaiYiHua\ECPay\Validations;

use Illuminate\Support\Facades\Validator;
use TsaiYiHua\ECPay\Constants\ECPayExtraPaymentInfo;
use TsaiYiHua\ECPay\Constants\ECPayPaymentMethod;
use TsaiYiHua\ECPay\Constants\ECPayPaymentMethodItem;

class PaymentValidation
{
    /**
     * Validation for post data
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator|\Illuminate\Validation\Validator
     */
    static public function postDataValidator($data)
    {
        /**
         * items[] = [
         *      'name' => 'abc',
         *      'qty' => 2,
         *      'unit' => 'piece',
         *      'price' => 50
         * ];
         */
        $validator = Validator::make($data, [
            'OrderId' => 'alpha_num|max:20',
            'ItemName' => 'required_if:Items,""',
            'TotalAmount' => 'required_if:Items,""',
            'Items' => 'required_if:ItemName,""',
            'ItemDescription' => 'required|max:200',
            'PaymentMethod' => 'in:'.implode(',', ECPayPaymentMethod::getConstantValues()->toArray()),
            'StoreId' => 'alpha_num|max:20',
            'ClientBackURL' => 'max:200',
            'ItemURL' => 'max:200',
            'Remark' => 'max:100',
            'ChooseSubPayment' => 'in:'.implode(',', ECPayPaymentMethodItem::getConstantValues()->toArray()),
            'OrderResultURL' => 'max:200',
            'NeedExtraPaidInfo' => 'in:'.implode(',', ECPayExtraPaymentInfo::getConstantValues()->toArray()),
            'IgnorePayment' => 'max:100',
            'PlatformID' => 'max:20',
            'CustomField1' => 'max:50',
            'CustomField2' => 'max:50',
            'CustomField3' => 'max:50',
            'CustomField4' => 'max:50',
            'ExpireDate' => 'int|min:1|max:60',
            'PaymentInfoURL' => 'url|max:200',
            'ClientRedirectURL' => 'url|max:200'
        ]);

        return $validator;
    }
}