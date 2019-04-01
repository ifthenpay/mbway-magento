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

namespace Ifthenpay\MbWay\Block\Checkout\Onepage\Success;

Class Response extends \Magento\Checkout\Block\Onepage\Success
{
    /**
     * Prepares block data
     *
     * @return void
     */
    protected $order;

    protected function _construct()
    {
        $this->setModuleName('Magento_Checkout');
        parent::_construct();
    }

    protected function prepareBlockData()
    {

        $this->order = $this->_checkoutSession->getLastRealOrder();
        $this->addData(
            [
                'phone_number' => $this->order->getPayment()->getAdditionalInformation('phone_number'),
                'grand_total' => $this->getFormatValue(),
                'order_id' => $this->order->getIncrementId(),
            ]
        );

    }

    private function getFormatValue()
    {
        return number_format($this->order->getGrandTotal(), '2', '.', ',');
    }

    public function isMethodIfthenpay()
    {
        if ($this->order->getPayment()->getMethod() == \Ifthenpay\MbWay\Model\Ui\ConfigProvider::CODE) {
            return true;
        }
        return false;
    }
}