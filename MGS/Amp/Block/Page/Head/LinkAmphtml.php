<?php
namespace MGS\Amp\Block\Page\Head;

class LinkAmphtml extends \Magento\Framework\View\Element\Template
{
	/**
	 * Default template for block
	 * @var string
	 */
    protected $_template = 'MGS_Amp::Magento_Theme/head/amp_link.phtml';
	
	/**
	 * Default template for block
	 * @var \MGS\Amp\Helper\Config
	 */
    protected $_configHelper;

    /**
     * @param \Magento\Catalog\Block\Product\Context        $context
     * @param \MGS\Amp\Helper\Config              			$configHelper
     * @param array                                         $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \MGS\Amp\Helper\Config $configHelper,
        array $data = []
    ) {
        $this->_configHelper = $configHelper;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve amp url of current page
     * @return string
     */
    public function getAmpUrl() {
        return $this->_configHelper->getAmpUrl();
    }
}