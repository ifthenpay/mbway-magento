<?php
class Ifthenpay_Mbway_Block_Info_Mbway extends Mage_Payment_Block_Info
{
  protected function _construct()
  {
    parent::_construct();
    $this->setTemplate('ifthenpay/mbway/info/mbway.phtml');
  }

  protected function _prepareSpecificInformation($transport = null)
  {
    if (null !== $this->_paymentSpecificInformation) 
    {
      return $this->_paymentSpecificInformation;
    }
     
    $data = array();
    if ($this->getInfo()->getMbwayPhone()) 
    {
      $data[Mage::helper('payment')->__('Nr. Telemóvel Mbway')] = $this->getInfo()->getMbwayPhone();
    }
 
    $transport = parent::_prepareSpecificInformation($transport);
     
    return $transport->setData(array_merge($data, $transport->getData()));
  }
}
