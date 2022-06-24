<?php
namespace Blueskytechco\SearchSuite\Block;

/**
 * Autocomplete class used for transport config data
 */
class Autocomplete extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Blueskytechco\SearchSuite\Helper\Data
     */
    protected $helperData;

	protected $_categoryHelper;
    /**
     * Autocomplete constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
		\Magento\Catalog\Helper\Category $categoryHelper,
        array $data = []
    ) {
		$this->_categoryHelper = $categoryHelper;
        parent::__construct($context, $data);
    }
	
	public function getCategories()
    {
        return $this->_categoryHelper->getStoreCategories(true , false, true);
    }
}
