<?php

class Ifthenpay_Mbway_Model_PaymentAction
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => Mage_Payment_Model_Method_Abstract::ACTION_ORDER,
                'label' => Mage::helper('mbway')->__('Order')
            ),
        );
    }
}
