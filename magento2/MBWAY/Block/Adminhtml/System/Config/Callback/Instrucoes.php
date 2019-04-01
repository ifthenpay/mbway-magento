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

/**
 * Renderer for Payments Advanced information
 */
namespace Ifthenpay\MbWay\Block\Adminhtml\System\Config\Callback;

class Instrucoes extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * Template path
     *
     * @var string
     */
    public $_template = 'system/config/callback/instrucoes.phtml';

    public $_ifthenpayMbHelper;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Ifthenpay\MbWay\Helper\Data $ifthenpayMbHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_ifthenpayMbHelper = $ifthenpayMbHelper;
    }

    /**
     * Render fieldset html
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $columns = $this->getRequest()->getParam('website') || $this->getRequest()->getParam('store') ? 5 : 4;
        return $this->_decorateRowHtml($element, "<td colspan='{$columns}'>" . $this->toHtml() . '</td>');
    }

    public function getMbWayKey()
    {
        return $this->_ifthenpayMbHelper->getMbWayKey();
    }

    public function getUrlCallback()
    {
        return $this->_storeManager->getStore()->getBaseUrl(). 
        'ifthenpaymbway/Callback/Check/key/[CHAVE_ANTI_PHISHING]/referencia/[REFERENCIA]/idpedido/[ID_TRANSACAO]/valor/[VALOR]/estado/[ESTADO]';
    }

    public function getAntiPhishingKey()
    {
        return $this->_ifthenpayMbHelper->getAntiPhishing();
    }
}
