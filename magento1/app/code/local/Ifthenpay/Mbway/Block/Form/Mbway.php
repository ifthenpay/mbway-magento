<?php
class Ifthenpay_Mbway_Block_Form_Mbway extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
      parent::_construct();
      $this->setTemplate('ifthenpay/mbway/form/mbway.phtml');
    }
}
