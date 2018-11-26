<?php
namespace TsaiYiHua\ECPay\Validations;

use Illuminate\Support\MessageBag;

class ItemValidation
{
    /**
     * Validation for items data
     * @param array $items
     * @param MessageBag $messages
     * @return MessageBag
     */
    public function ItemValidation(array $items)
    {
        $messages = new MessageBag();
        $i = 0;
        foreach($items as $item) {
            if (!isset($item['name'])) {
                $messages->add('Item.name:' . $i, 'Item Name can not leave be blank');
            }
            if (!isset($item['qty'])) {
                $messages->add('Item.qty:' . $i, 'Quantity can not leave be blank');
            } else {
                if (!is_numeric($item['qty'])) {
                    $messages->add('Item.qty:' . $i, 'Quantity must be numeric');
                }
            }
            if (!isset($item['unit'])) {
                $messages->add('Item.unit:' . $i, 'Item Unit can not leave be blank');
            } else {
                if (strlen($item['unit']) > 6) {
                    $messages->add('Item.unit:' . $i, 'Item Unit can not great than 6 characters');
                }
            }
            if (!isset($item['price'])) {
                $messages->add('Item.price:' . $i, 'Item Price can not leave be blank');
            } else {
                if (!is_numeric($item['price'])) {
                    $messages->add('Item.price:' . $i, 'Item Price must be numeric');
                }
            }
            $i++;
        }
        return $messages;
    }
}