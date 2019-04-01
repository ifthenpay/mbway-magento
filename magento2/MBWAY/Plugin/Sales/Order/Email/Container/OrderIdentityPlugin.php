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

namespace Ifthenpay\MbWay\Plugin\Sales\Order\Email\Container;

class OrderIdentityPlugin
{
    /**
     * @var \Magento\Checkout\Model\Session $checkoutSession
     */
    protected $checkoutSession;

    /**
     * @param \Magento\Checkout\Model\Session $checkoutSession
     *
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession
    )
    {
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param \Magento\Sales\Model\Order\Email\Container\OrderIdentity $subject
     * @param callable $proceed
     * @return bool
     */
    public function aroundIsEnabled(\Magento\Sales\Model\Order\Email\Container\OrderIdentity $subject, callable $proceed)
    {
        $returnValue = $proceed();

        $forceOrderMailSentOnSuccess = $this->checkoutSession->getForceOrderMailSentOnSuccess();
        if(isset($forceOrderMailSentOnSuccess) && $forceOrderMailSentOnSuccess)
        {
            if($returnValue)
                $returnValue = false;
            else
                $returnValue = true;

            $this->checkoutSession->unsForceOrderMailSentOnSuccess();
        }

        return $returnValue;
    }
}