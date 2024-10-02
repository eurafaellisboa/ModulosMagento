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

    // Verifique se o cliente é um "Representante" e, se for, não adicione este método
    if ($this->customerSession->isLoggedIn()) {
        $customerGroupId = $this->customerSession->getCustomer()->getGroupId();

        // Verifique se o cliente é um "Representante" (você pode substituir 4 pelo ID do grupo de clientes "Representante")
        if ($customerGroupId == 4) {
            return $result;
        }
    }

    $result->append($method);

    return $result;
}

}
