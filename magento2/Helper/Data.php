<?php
/**
* Ifthenpay_Multibanco module dependency
*
* @category    Gateway Payment
* @package     Ifthenpay_Multibanco
* @author      Manuel Rocha
* @copyright   Manuel Rocha (http://www.manuelrocha.biz)
* @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/

namespace Ifthenpay\MbWay\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const IFTHENPAY_MBWAYKEY = 'payment/ifthenpay_mb_way/mbway_key'; 	 
    const IFTHENPAY_ANTIPHISHING = 'payment/ifthenpay_mbway/chave_anti_phishing';

    public $_configTable;
    public $connection;
    public $_orderTable;
    public $_orderFactory;
 
    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Sales\Model\OrderFactory $orderFactory
    ) {
        $this->_configTable = $resource->getTableName('core_config_data');
        $this->_orderTable = $resource->getTableName('sales_order');
        $this->_orderFactory = $orderFactory;
        $this->connection = $resource->getConnection();

        parent::__construct($context);
    }

    public function getMbWayKey()
    {
        return $this->scopeConfig->getValue(
            self::IFTHENPAY_MBWAYKEY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getAntiPhishing()
    {
        $chaveap = $this->scopeConfig->getValue(
            self::IFTHENPAY_ANTIPHISHING,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if ($chaveap == "" || $chaveap == null) {
            $chaveap=md5(time());

            $bindValues = ['path' => self::IFTHENPAY_ANTIPHISHING ];
            $select = $this->connection->select()->from($this->_configTable)->where('path = :path');
            $exists = $this->connection->fetchOne($select, $bindValues);

            $bind = ['value' => $chaveap];

            if ($exists) {
                $this->connection->update($this->_configTable, $bind, ['path=?' => self::IFTHENPAY_ANTIPHISHING]);
            } else {
                $bind['path'] = self::IFTHENPAY_ANTIPHISHING;
                $bind['value'] = $chaveap;
                $this->connection->insert($this->_configTable, $bind);
            }
        }

        return $chaveap;
    }

    public function checkIfAntiPhishingIsValid($ap)
    {
        return (
            $ap == $this->scopeConfig->getValue(
                self::IFTHENPAY_ANTIPHISHING,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )
        );
    }
}
