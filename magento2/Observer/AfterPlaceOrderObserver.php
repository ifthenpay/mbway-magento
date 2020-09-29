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

namespace Ifthenpay\MbWay\Observer;

use Magento\Payment\Observer\AbstractDataAssignObserver;

class AfterPlaceOrderObserver extends AbstractDataAssignObserver
{
    /**
     * Order Model
     *
     * @var \Magento\Sales\Model\Order $order
     */
    protected $order;

    public function __construct(
        \Magento\Sales\Model\Order $order
    )
    {
        $this->order = $order;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $orderId = $observer->getEvent()->getOrderIds();
        $order = $this->order->load($orderId);
        $currentState = $order->getState();
        
        $pay = $order->getPayment();
        $method = $pay->getMethodInstance();
        $methodTitle = $method->getTitle();

        $save = false;
        if(stripos($methodTitle, "way") !== FALSE) {
            if ($currentState !== $order::STATE_NEW) {
                $order->setState($order::STATE_PENDING_PAYMENT);
                $order->setStatus('pending');
                $save = true;
            }
            if ($save) {
                $order->save();
            }
        }
    }
}
