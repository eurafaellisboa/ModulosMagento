<?php

namespace MGS\Amp\Observer;
use Magento\Framework\Event\ObserverInterface;

class CoreLayoutRenderElement implements ObserverInterface
{
    /**
     * @var \MGS\Amp\Helper\Config
     */
    protected $_configHelper;

    /**
     * Array of elements names that need to disable for output
     * @var array
     */
    protected $_disabledElements = [];

    /**
     * @param \Magento\Framework\App\Response\Http $response
     * @param \MGS\Amp\Helper\Config $configHelper
     */
    public function __construct(
        \MGS\Amp\Helper\Config $configHelper
    ) {
        $this->_configHelper = $configHelper;
    }

    /**
     * @param  \Magento\Framework\Event\Observer
     * @return \Magento\Framework\Event\Observer this object
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /**
         * Checking module status
         */
        if (!$this->_configHelper->enableAmp()){
            return $this;
        }

        /**
         * Get element html and replace it
         */
		if ($this->_configHelper->isAmpCall()) {
            $currentElementName = $observer->getElementName();
            $transport = $observer->getTransport();

            /**
             * Disable output for disallowed elements
             */
            if ($this->_disableOutput($currentElementName)) {
                $html = '';
            } else {
                $html = $transport->getOutput();
            }

            /**
             * Set final Html output
             */
            $transport->setOutput($html);
        }

        return $this;
    }

    /**
     * @param  string $elementName
     * @return boolean
     */
    protected function _disableOutput($elementName)
    {
        return in_array($elementName, $this->_disabledElements) ? true : false;
    }

}
