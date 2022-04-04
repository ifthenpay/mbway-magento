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

namespace Ifthenpay\MbWay\Gateway\Response;

use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Response\HandlerInterface;

class TxnIdHandler implements HandlerInterface
{
    /**
     * Handles transaction id
     *
     * @param array $handlingSubject
     * @param array $response
     * @return void
     */

    public function handle(array $handlingSubject, array $response)
    {
        $response = $response[0];

        if (!isset($handlingSubject['payment'])
            || !$handlingSubject['payment'] instanceof PaymentDataObjectInterface
        ) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }
        /** @var PaymentDataObjectInterface $paymentDO */
        $paymentDO = $handlingSubject['payment'];
        $payment = $paymentDO->getPayment();
        $order = $paymentDO->getOrder();
               
        if ($response->SetPedidoResult->IdPedido && $response->SetPedidoResult->Valor == $order->getGrandTotalAmount()) {
            $payment->setIsTransactionClosed(false);
            $payment->setIsTransactionPending(true);
            $payment->setAdditionalInformation('ID Pedido', $response->SetPedidoResult->IdPedido);
            $payment->setTransactionId($payment->getAdditionalInformation('ID Pedido'));
            $this->setMbWayErrorMessage($response, $payment);
        } else {
            $this->setMbWayErrorMessage($response, $payment);
        }
    }
    public function setMbWayErrorMessage($response, $payment)
    {
        if ($response->SetPedidoResult->Estado === '000') {
            $payment->setAdditionalInformation('Mensagem', 'Pedido mbway colocado com sucesso');
        } else {
            $msg = $response->SetPedidoResult->Estado . ' ' . $response->SetPedidoResult->MsgDescricao;
            $payment->setAdditionalInformation('Mensagem', $msg);
        }
    }
}
