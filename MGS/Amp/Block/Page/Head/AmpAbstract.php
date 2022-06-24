<?php

namespace MGS\Amp\Block\Page\Head;

class AmpAbstract extends \Magento\Framework\View\Element\Template
{
    const DEFAULT_ASSET_NAME = 'pramp-asset';

    /**
     * @var Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var MGS\Amp\Helper\Config
     */
    protected $_configHelper;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $coreRegistry,
     * @param \Zemez\Amp\Helper\Data $helper,
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \MGS\Amp\Helper\Config $configHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_coreRegistry = $coreRegistry;
        $this->_configHelper = $configHelper;
    }

    /**
     * Retrieve common data
     * @return array
     */
    public function getAmpParams()
    {
        return [
            'title' => $this->pageConfig->getTitle()->get(),
            'description' => mb_substr($this->pageConfig->getDescription(), 0, 200, 'UTF-8'),
        ];

    }

    /**
     * Preparing global layout
     * @return $this
     */
    protected function _prepareLayout() {
        $this->pageConfig->addRemotePageAsset(
            $this->_configHelper->getCanonicalUrl(),
            'canonical',
            ['attributes' => ['rel' => 'canonical']],
            self::DEFAULT_ASSET_NAME
        );

        return parent::_prepareLayout();
    }

}
