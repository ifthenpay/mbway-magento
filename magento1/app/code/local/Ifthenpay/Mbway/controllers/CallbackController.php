<?php

class Ifthenpay_Mbway_CallbackController extends Mage_Core_Controller_Front_Action
{
    public function checkAction()
    {
      $key = $this->getRequest()->getParam('key');
      $referencia = $this->getRequest()->getParam('referencia');
      $idpedido = $this->getRequest()->getParam('idpedido');
      $valor = $this->getRequest()->getParam('valor');
      $estado = $this->getRequest()->getParam('estado');

      $db = Mage::getSingleton('core/resource')->getConnection('core_write');
      $query = "SELECT * FROM ifthenpay_mbway_config ORDER BY id ASC LIMIT 1";

      $stmt = $db->prepare($query);
      $stmt->execute();
      
      if ($result = $stmt->fetch()) {
        if( $key === $result["antiphishing"]) {
          $order = Mage::getModel('sales/order')->load($referencia, 'increment_id');
          $last_trans_id = $order->getPayment()->getData('last_trans_id');
          $amount_ordered = $order->getPayment()->getData('amount_ordered');

          if ($last_trans_id === $idpedido && number_format($valor, 2) === number_format($amount_ordered, 2)) {
            $order->setData('state', "complete");
            $order->setStatus("processing");
            $order->sendOrderUpdateEmail();
      
            $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();
            if (!$invoice->getTotalQty()) {
              Mage::throwException(Mage::helper('core')->__('Impossível criar fatura...'));
            }
            $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_OFFLINE);
            $invoice->register();
            $transactionSave = Mage::getModel('core/resource_transaction')
              ->addObject($invoice)
              ->addObject($invoice->getOrder());
            $transactionSave->save();
              
            $history = $order->addStatusHistoryComment('Encomenda marcada como paga pelo sistema de pagamento Ifthenpay Mbway.', false);
            $history->setIsCustomerNotified(true);
            $order->save();

            //Sucesso
            echo "K200";
          }
          else
          {
            //Não sabemos o que é
            echo "ERRO K500";
          }
        }
        else
        {
          //Chave Anti-Phishing Inválida
          echo "ERRO K203";
        }
      }else{
        //Chave Anti-Phishing não configurada. Tem de Activar primeiro o Callback.
        echo "ERRO K404";
      }
      die();
    }
}
