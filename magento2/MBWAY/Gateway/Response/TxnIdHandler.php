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
        if ($response->SetPedidoResult->IdPedido) {
            $payment->setAdditionalInformation('ID Pedido', $response->SetPedidoResult->IdPedido);
            $payment->setTransactionId($payment->getAdditionalInformation('ID Pedido'));
            if ($response->SetPedidoResult->Estado !== '000') {
                $this->setMbWayErrorMessage($response, $payment);
            }
        } else {
            $this->setMbWayErrorMessage($response, $payment);
        }
        $payment->setIsTransactionClosed(false);
        if ($response->SetPedidoResult->IdPedido && $response->SetPedidoResult->Valor == $order->getGrandTotalAmount()) {
            $payment->setIsTransactionClosed(true);
        }
    }
    public function setMbWayErrorMessage($response, $payment)
    {
        $msg = $response->SetPedidoResult->Estado . ' ' . $response->SetPedidoResult->MsgDescricao;
        $payment->setAdditionalInformation('Erro', $msg);
    }
}
