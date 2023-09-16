<?php
namespace Digitaria\CustomFreeShipping\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Rate\Result;
use Magento\Customer\Model\Session as CustomerSession;

class CustomFreeShipping extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements
    \Magento\Shipping\Model\Carrier\CarrierInterface
{
    /**
     * @var string
     */
    protected $_code = 'customfreeshipping';

    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    protected $_rateResultFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    protected $_rateMethodFactory;

    /**
     * @var Data
     */
    private $customFreeShippingHelper;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * Shipping constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface          $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory  $rateErrorFactory
     * @param \Psr\Log\LoggerInterface                                    $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory                  $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param CustomerSession                                             $customerSession
     * @param array                                                       $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        CustomerSession $customerSession,
        array $data = []
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->customerSession = $customerSession;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * get allowed methods
     * @return array
     */
    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('name')];
    }

    /**
     * @return float
     */
    private function getShippingPrice()
    {
        $configPrice = $this->getConfigData('price');

        $shippingPrice = $this->getFinalPriceWithHandlingFee($configPrice);

        return $shippingPrice;
    }

    /**
     * @param RateRequest $request
     * @return bool|Result
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        // Obtenha os IDs dos grupos de clientes permitidos na configuração
        $allowedCustomerGroups = $this->getConfigData('customergroup');
        if ($this->customerSession->isLoggedIn()) {
            // Obtenha o ID do grupo de clientes do cliente atual
            $customerGroupId = $this->customerSession->getCustomer()->getGroupId();

            // Obtenha a lista de grupos de clientes permitidos a partir da configuração
            $allowedCustomerGroups = explode(',', $this->getConfigData('customergroup'));

            // Converta os IDs de grupo de clientes em números inteiros
            $allowedCustomerGroups = array_map('intval', $allowedCustomerGroups);
    
            // Verifique se o ID do grupo de clientes está na lista de grupos permitidos
            if (!in_array($customerGroupId, $allowedCustomerGroups)) {
                $logger->info('Grupo de cliente não permitido.');
                return false; // Grupo de cliente não permitido
            }
        } else {
    
        }

        $zipRanges = $this->getConfigData('ziprange');

        if (!empty($zipRanges)) { 
            $zipRangesArray = explode("\n", $zipRanges);
            $customerZip = $request->getDestPostcode();

            $methodFound = false;

            foreach ($zipRangesArray as $zipRange) {
                $rangeData = explode(',', $zipRange);
                if (count($rangeData) == 3) {
                    $startZip = trim($rangeData[0]);
                    $endZip = trim($rangeData[1]);

                    if ($customerZip >= $startZip && $customerZip <= $endZip) {
                        $minimumValue = floatval(trim($rangeData[2]));

                        // Obtenha o subtotal do carrinho
                        $subtotal = $request->getPackageValue();

                        // Obtenha o preço do produto em si
                        $itemPrice = 0;
                        foreach ($request->getAllItems() as $item) {
                            $itemPrice += $item->getBaseRowTotal();
                        }

                        if ($subtotal >= $minimumValue || $itemPrice >= $minimumValue) {
                            $methodFound = true;
                            break;
                        }
                    }
                }
            }

            if (!$methodFound) {
                return false;
            }
        }

        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->_rateResultFactory->create();

        /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
        $method = $this->_rateMethodFactory->create();

        $method->setCarrier($this->_code);
        $method->setCarrierTitle($this->getConfigData('title'));

        $method->setMethod($this->_code);
        $method->setMethodTitle($this->getConfigData('name'));

        $amount = $this->getShippingPrice();

        $method->setPrice($amount);
        $method->setCost($amount);

        $result->append($method);

        return $result;
    }
}
