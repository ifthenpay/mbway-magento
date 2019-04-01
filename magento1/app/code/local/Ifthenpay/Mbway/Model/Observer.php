<?php
/* Our class name should follow the directory structure of our Observer.php model, starting from the namespace, replacing directory separators with underscores. The directory of ousr Observer.php is following:
app/code/local/Mage/ProductLogUpdate/Model/Observer.php */
class Ifthenpay_Mbway_Model_Observer
{
  // Magento passes a Varien_Event_Observer object as the first parameter of dispatched events.
  public function setPaymentState(Varien_Event_Observer $observer)
  {
      // TODO
  }
  // Magento passes a Varien_Event_Observer object as the first parameter of dispatched events.
  public function setInvoice(Varien_Event_Observer $observer)
  {
      // TODO
  }
}
