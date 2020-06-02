<?php
/**
* Ifthenpay_MbWay module dependency
*
* @category    Gateway Payment
* @package     Ifthenpay_MbWay
* @author      Ifthenpay
* @copyright   Ifthenpay (http://www.ifthenpay.com)
* @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/

namespace Ifthenpay\MbWay\Controller\Callback;

use \Magento\Framework\App\Action\Action;

use Ifthenpay\MbWay\Model\Service\CreateInvoiceService;


class Check extends Action
{

    protected $createInvoiceService;


    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        CreateInvoiceService $createInvoiceService
    ) {
        parent::__construct($context);
        $this->createInvoiceService = $createInvoiceService;
    }


    public function execute()
    {

        $response = $this->allAction();
        $JsonFactory = $this->_objectManager->get('Magento\Framework\Controller\Result\JsonFactory');
        $result = $JsonFactory->create();
        $result = $result->setData($response);
        if (!isset($response['success']))
            $result->setHttpResponseCode(403);
        return $result;
    }

    private function allAction()
    {

        $callBack_params = (object)$this->getRequest()->getParams();

        $orderFactory = $this->_objectManager->get('Magento\Sales\Model\OrderFactory');
        $order = $orderFactory->create()->loadByIncrementId($callBack_params->referencia);
        //$last_trans_id = $order->getPayment()->getData('last_trans_id');
        $order_id = $order->getId();
        $order_status = $order->getStatus();
        $order_value = number_format($order->getGrandTotal(), 2);
        $chave_anti_phishing = $this->_objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('payment/ifthenpay_mbway/chave_anti_phishing');

        if (!$order_id)
            return ["error" => 'A encomenda não foi encontrada'];

        if ($order_status === 'canceled')
            return ['error' => 'Não foi possivel concluir o pagamento porque a encomenda foi cancelada.'];

        if ($callBack_params->key !== $chave_anti_phishing)
            return ["error" => "Chave anti-phishing inválida"];

        if ($order_value !== $callBack_params->valor)
            return ["error" => "O valor da encomenda não corresponde ao valor pago."];

        if ($order->getBaseTotalDue() == 0)
            return ["error" => "A encomenda já foi paga."];

        if ($order->getBaseTotalDue() < $callBack_params->valor)
            return ["error" => "O valor da encomenda é inferior ao valor pago!"];

        if (number_format($callBack_params->valor, 2) === $order_value) {
            
            $order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING)
                ->setStatus($order->getConfig()->getStateDefaultStatus(\Magento\Sales\Model\Order::STATE_PROCESSING));

            $order->save();
            
            $this->createInvoiceService->createInvoice($order);
            
            return ["success" => true, "message" => "Pagamento foi concluido com sucesso."];
        } else {
            return ["error" => "O ID não corresponde a nenhuma transação desta encomenda."];
        }
    }
    
    private function capture($order)
    {
        $payment = $order->getPayment();
        try {
            $payment->capture();
        } catch (\Exception $e) {
            return ["error" => $e->getMessage()];
        }
        $order->save();
        return ["success" => true, "message" => "Pagamento foi capturado com sucesso."];
    }
}
