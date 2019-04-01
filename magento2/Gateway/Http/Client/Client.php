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


namespace Ifthenpay\MbWay\Gateway\Http\Client;

use Magento\Framework\Webapi\Soap\ClientFactory;
use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\ConverterInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Magento\Payment\Model\Method\Logger;

/**
 * Class Soap
 * @package Magento\Payment\Gateway\Http\Client
 * @api
 */
class Client implements ClientInterface
{
    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var ConverterInterface | null
     */
    private $converter;
    /**
     * @var ClientFactory
     */
    private $clientFactory;

    protected $_messageManager;


    /**
     * @param Logger $logger
     * @param ClientFactory $clientFactory
     * @param ConverterInterface | null $converter
     */
    public function __construct(\Magento\Framework\Message\ManagerInterface $messageManager,Logger $logger, ClientFactory $clientFactory, ConverterInterface $converter = null)
    {
        $this->logger = $logger;
        $this->converter = $converter;
        $this->clientFactory = $clientFactory;
        $this->_messageManager = $messageManager;
    }

    /**
     * Places request to gateway. Returns result as ENV array
     *
     * @param TransferInterface $transferObject
     * @return array
     * @throws \Magento\Payment\Gateway\Http\ClientException
     * @throws \Magento\Payment\Gateway\Http\ConverterException
     * @throws \Exception
     */
    public function placeRequest(TransferInterface $transferObject)
    {
        $client = $this->clientFactory->create($transferObject->getClientConfig()['wsdl'], array(
            'trace' =>true,
            'connection_timeout' => 5000,
            'cache_wsdl' => WSDL_CACHE_NONE,
            'keep_alive' => false,
        ));

        try {
            $client->__setSoapHeaders($transferObject->getHeaders());
            $response = $client->__soapCall($transferObject->getMethod(), [$transferObject->getBody()]);
            $result = $this->converter ? $this->converter->convert($response) : [$response];
            
            if ($result[0]->SetPedidoResult->Estado === '000') {
                $msg = $result[0]->SetPedidoResult->MsgDescricao . ' Confirme o pagamento na sua app MB WAY.';
                $this->_messageManager->addSuccessMessage(__($msg));
            } else {
                $this->_messageManager->addWarningMessage(__('Ocorreu um erro por favor contacte o dono da loja.'));
            }

        } catch (\Exception $e) {
            throw $e->getMessage();
        }
        return $result;
    }
}
