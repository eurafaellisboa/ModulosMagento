<?php

namespace MGS\Amp\Block\Page\Head;

class AmpCategory extends AmpAbstract
{
	/**
     * @var \Magento\Catalog\Helper\Category
     */
    protected $_categoryHelper;

	/**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $coreRegistry,
     * @param \MGS\Amp\Helper\Config $configHelper,
     * @param \Magento\Catalog\Helper\Category $categoryHelper
     * @param array $data
     */
    public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \MGS\Amp\Helper\Config $configHelper,
        \Magento\Catalog\Helper\Category $categoryHelper,
        array $data = []
    ) {
        parent::__construct($context, $coreRegistry, $configHelper, $data);
        $this->_categoryHelper = $categoryHelper;
    }

	/**
	 * Retrieve additional data
	 * @return array
	 */
    public function getAmpParams()
    {
        $params = parent::getAmpParams();
        $_category = $this->getCategory();

        return array_merge($params, [
            'type' => 'category',
            'url' => $this->_configHelper->getCanonicalUrl($_category->getUrl()),
            'image' => (string)$_category->getImageUrl(),
        ]);
    }

    /**
     * Retrieve current category object
     * @return \Magento\Catalog\Model\Category
     */
    public function getCategory()
    {
        return $this->_coreRegistry->registry('current_category');
    }

	/**
     * {@inheritDoc}
     */
    protected function _prepareLayout()
    {
        $category = $this->getCategory();
        $categoryUrl = false;

        if ($category && $category->getId()) {
            $categoryUrl = $category->getUrl();
        }

        if ($categoryUrl && !$this->_categoryHelper->canUseCanonicalTag()) {
            $this->pageConfig->addRemotePageAsset(
                $this->_configHelper->getCanonicalUrl($categoryUrl),
                'canonical',
                ['attributes' => ['rel' => 'canonical']],
				self::DEFAULT_ASSET_NAME
            );
        }

        return parent::_prepareLayout();
    }
}
