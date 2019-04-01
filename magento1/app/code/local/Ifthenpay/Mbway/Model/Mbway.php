<?php
class Ifthenpay_Mbway_Model_Mbway extends Mage_Payment_Model_Method_Abstract
{
  protected $_code  = 'mbway';
  protected $_formBlockType = 'mbway/form_mbway';
  protected $_infoBlockType = 'mbway/info_mbway';
     
  protected $_isGateway = true;

  protected $_canAuthorize = true;

  protected $_canCapture = false;

  protected $_canCapturePartial = false;

  protected $_canRefund = false;

  protected $_canVoid = false;

  protected $_canUseInternal = true;

  protected $_canUseCheckout = true;

  protected $_canUseForMultishipping  = true;

  protected $_canSaveCc = false; // WARNING: you cant keep card data unless you have PCI complience licence

  public function assignData($data)
  {
    $info = $this->getInfoInstance();
     
    if ($data->getMbwayPhone())
    {
      $info->setMbwayPhone($data->getMbwayPhone());
    }
 
    return $this;
  }

  public function validate()
  {
    parent::validate();
    $info = $this->getInfoInstance();
     
    if (!$info->getMbwayPhone())
    {
      $errorCode = 'invalid_data';
      $errorMsg = $this->_getHelper()->__("Nr. Telemóvel Mbway é um campo obrigatório.\n");
    } else {
      $valor = $info->getMbwayPhone();

      if (strlen($valor) < 9 || strlen($valor) > 9 || !is_numeric($valor)) {
        $errorCode = 'invalid_data';
        $errorMsg = $this->_getHelper()->__("Tem de indicar um Nr. Telemóvel Mbway válido.\n");
      }
    }
 
    if ($errorMsg) 
    {
      Mage::throwException($errorMsg);
    }
 
    return $this;
  }

  public function order(Varien_Object $payment, $amount)
  {
    $mbway_key = $this->getConfigData('mbwaykey');
    $nr_mbway = $this->getInfoInstance()->getMbwayPhone();
    $nr_encomenda = $payment->getOrder()->getIncrementId();
    $store = Mage::app()->getStore();
    $descricao = 'Enc.: #' . $nr_encomenda . ' | ' . $store->getName();
    $info = $this->getInfoInstance();

    $resposta_api = $this->callIfthenpayMbwayApi($mbway_key, $nr_mbway, $nr_encomenda, $descricao, $amount);

    if(!isset($resposta_api)) {
      $errorMsg = $this->_getHelper()->__('Ocorreu um erro no pedido ao serviço Mbway. Tente novamente. Caso se repita entre em contacto connosco.');
    } else {
      if ($resposta_api->SetPedidoResult->Estado !== "000")
      {
        $errorMsg = $this->_getHelper()->__($resposta_api->SetPedidoResult->MsgDescricao);
      } else {
        $payment->setTransactionId($resposta_api->SetPedidoResult->IdPedido);
        $payment->setTransactionAdditionalInfo(
          Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS,
          array(
            'referencia'=>$nr_encomenda,
            'resposta'=>$resposta_api->SetPedidoResult->Estado . ' - ' . $resposta_api->SetPedidoResult->MsgDescricao,
            'method'=> 'mbway', 
            "data_hora_pedido"=>$resposta_api->SetPedidoResult->DataHora,
            "id_pedido"=>$resposta_api->SetPedidoResult->IdPedido
          )
        );
      }
    }
				
    if(isset($errorMsg)){
      $payment->setSkipOrderProcessing(true);
      Mage::log("Mbway Erro: ".$errorMsg, null, 'ifthenpay_mbway.log');
      Mage::getSingleton('core/session')->addError($errorMsg);
      Mage::throwException($errorMsg);
    }

    return $this;
  }

  private function callIfthenpayMbwayApi($mbway_key, $nr_mbway, $nr_encomenda, $descricao, $amount) {
    $url_api = "https://www.ifthenpay.com/mbwayws/IfthenPayMBW.asmx?wsdl";
    $dados_api = array(
      "MbWayKey" => $mbway_key,
      "canal" => "03",
      "referencia" => $nr_encomenda,
      "valor" => $amount,
      "nrtlm" => $nr_mbway,
      "email" => "",
      "descricao" => $descricao
    );

    $soap_request = new SoapClient($url_api, array('cache_wsdl' => WSDL_CACHE_NONE));

    $soap_request_result = $soap_request->SetPedido($dados_api);

    return $soap_request_result;
  }
}
