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

\Magento\Framework\Component\ComponentRegistrar::register(
    \Magento\Framework\Component\ComponentRegistrar::MODULE,
    'Ifthenpay_MbWay',
    __DIR__
);
