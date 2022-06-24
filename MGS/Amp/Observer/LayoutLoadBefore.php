<?php
namespace MGS\Amp\Observer;
use Magento\Framework\Module\StatusFactory;

class LayoutLoadBefore implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Framework\App\Response\Http
     */
    protected $_response;

    /**
     * @var \MGS\Amp\Helper\Config
     */
    protected $_configHelper;

    /**
     * @param \Magento\Framework\App\Response\Http $response
     * @param \Zemez\Amp\Helper\Data $dataHelper
     */
    public function __construct(
        \Magento\Framework\App\Response\Http $response,
        \MGS\Amp\Helper\Config $configHelper
    ) {
        $this->_response = $response;
        $this->_configHelper = $configHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->_configHelper->enableAmp()){
            return;
        }

        $currentFullAction = $this->_configHelper->getFullActionName();
        $update = $observer->getEvent()->getLayout()->getUpdate();
		
		/* Not Understand */
        if($this->_configHelper->isOnlyOptionsRequest()) {
            $update->addHandle('mgsamp_catalog_product_view_only_options');
            return true;
        }

        // Check get parameter amp
        if ($this->_configHelper->isAmpCall()) {
            if (function_exists('newrelic_disable_autorum')) {
                newrelic_disable_autorum();
            }

            //  Add layout handlers
            $update->addHandle('mgs_amp_layout');
            foreach ($update->getHandles() as $handleName) {
                $update->addHandle('mgsamp_' . $handleName);
            }
        }

        /**
         * Add layout changes
         */
        if ($this->_configHelper->isAllowedPage()) {
            $update->addHandle('mgsamp_ampurl_page');
        }

    }

}
