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

namespace Ifthenpay\MbWay\Block;

class Info extends \Magento\Payment\Block\Info
{

    public function getSpecificInformation()
    {
        $informations['Telemovel'] = $this->getInfo()->getAdditionalInformation('phone_number');
        $informations['ID Pedido'] = $this->getInfo()->getAdditionalInformation('ID Pedido');
        $informations['Erro'] = $this->getInfo()->getAdditionalInformation('Erro');
        return (object)$informations;
    }

    public function getMethodCode()
    {
        return $this->getInfo()->getMethodInstance()->getCode();
    }
}
