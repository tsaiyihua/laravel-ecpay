<?php
namespace TsaiYiHua\ECPay\Validations;

use Illuminate\Support\Facades\Validator;
use TsaiYiHua\ECPay\Constants\ECPayCarruerType;
use TsaiYiHua\ECPay\Constants\ECPayClearanceMark;
use TsaiYiHua\ECPay\Constants\ECPayDonation;
use TsaiYiHua\ECPay\Constants\ECPayInvType;
use TsaiYiHua\ECPay\Constants\ECPayPrintMark;
use TsaiYiHua\ECPay\Constants\ECPayTaxType;

class InvoiceValidation
{
    /**
     * Validation for invoice post data
     * @param $data
     * @return \Illuminate\Contracts\Validation\Validator|\Illuminate\Validation\Validator
     */
    static public function invoiceValidator($data)
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
            'Items' => 'required|array',
            'OrderId' => 'alpha_num|max:30',
            'CustomerID' => 'alpha_dash|max:20',
            'CustomerIdentifier' => 'int|max:8',
            'CustomerName' => 'required_if:Print,1|max:60',
            'CustomerAddr' => 'required_if:Print,1|max:200',
            'CustomerPhone' => 'required_if:CustomerEmail,null|max:20',
            'CustomerEmail' => 'required_if:CustomerPhone,null|max:200',
            'ClearanceMark' => 'in:'.implode(',', ECPayClearanceMark::getConstantValues()->toArray()),
            'TaxType' => 'in:'.implode(',', ECPayTaxType::getConstantValues()->toArray()),
            'CarruerType' => 'in:'.implode(',', ECPayCarruerType::getConstantValues()->toArray()),
            'CarruerNum' => 'max:64',
            'Donation' => 'in:'.implode(',', ECPayDonation::getConstantValues()->toArray()),
            'LoveCode' => 'required_if:Donation,1|max:7',
            'Print' => 'in:'.implode(',', ECPayPrintMark::getConstantValues()->toArray()),
            'DelayDay' => 'int|min:0|max:15',
            'InvType' => 'in:'.implode(',', ECPayInvType::getConstantValues()->toArray())
        ]);
        return $validator;
    }
}