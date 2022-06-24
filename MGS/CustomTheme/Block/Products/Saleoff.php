<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MGS\CustomTheme\Block\Products;

/**
 * Main contact form block
 */
class Saleoff extends \Magento\Catalog\Block\Product\AbstractProduct
{
	/**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * Catalog product visibility
     *
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_catalogProductVisibility;

    /**
     * Product collection factory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;
	
	/**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
	
	protected $_count;
	
	/**
     * @var \Magento\Framework\Url\Helper\Data
     */
    protected $urlHelper;
	
	/**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    protected $formKey;
	
	protected $_resource;
	
	protected $_productloader;  
	
	protected $_attributeCollection;
	
	protected $_date;
    /**
     * @param Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Framework\App\Http\Context $httpContext,
		\Magento\Framework\ObjectManagerInterface $objectManager,
		\Magento\Framework\Url\Helper\Data $urlHelper,
		\Magento\Framework\Stdlib\DateTime\DateTime $date,
		\Magento\Framework\Data\Form\FormKey $formKey,
		\Magento\Framework\App\ResourceConnection $resource,
		\Magento\Catalog\Model\ProductFactory $_productloader,
		\Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeCollection,
        array $data = []
    ) {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_catalogProductVisibility = $catalogProductVisibility;
		$this->_objectManager = $objectManager;
        $this->httpContext = $httpContext;
		$this->urlHelper = $urlHelper;
		$this->_date = $date;
		$this->_resource = $resource;
		$this->_productloader = $_productloader;
		$this->_attributeCollection = $attributeCollection;
		$this->formKey = $formKey;
        parent::__construct(
            $context,
            $data
        );
    }
	
	public function getCacheKeyInfo()
    {
        return [
            'MPANEL_SALEOFF_LIST',
            $this->_storeManager->getStore()->getId(),
            $this->_design->getDesignTheme()->getId(),
            $this->httpContext->getValue(CustomerContext::CONTEXT_GROUP),
            'template' => $this->getTemplate()
        ];
    }
	
	public function _construct()
    {
        parent::_construct();
        $collection = $this->_productCollectionFactory->create();
        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
		
		
        $collection = $this->_addProductAttributesAndPrices($collection)
			->addAttributeToSelect(['image', 'name', 'short_description'])
            ->addStoreFilter()
			->addFinalPrice()
            ->addAttributeToSort('created_at', 'desc');
		
		$collection->getSelect()->where('price_index.final_price < price_index.price');
        $collection->setPageSize($this->getLimitProduct())
            ->setCurPage($this->getCurrentPage())
			->setOrder('entity_id', 'DESC');
			
        $this->setCollection($collection);
    }
	
	protected function _prepareLayout()
    {
        $this->pageConfig->addBodyClass('page-saleoff');

        if ($this->getCollection()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'page.sale.list.pager'
            );
			
            $pager->setLimit($this->getLimitProduct())->setCollection(
                $this->getCollection()
            );
            $this->setChild('pager', $pager);
        }
        return parent::_prepareLayout();
    }
	
	public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
	
	/**
     * Product collection initialize process
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|Object|\Magento\Framework\Data\Collection
     */
    
	public function setCollection($collection)
    {
        $this->_collection = $collection;
        return $this->_collection;
    }

    public function getCollection()
    {
        return $this->_collection;
    }
	
	public function getCurrentPage(){
		return $this->getRequest()->getParam('p') ? $this->getRequest()->getParam('p') : 1;
	}
	
	public function getLimitProduct(){
		return $this->getData('limit') ? $this->getData('limit') : 12;
	}
	
	public function getLoadProduct($id)
    {
        return $this->_productloader->create()->load($id);
    }
	
	public function getModel($model){
		return $this->_objectManager->create($model);
	}
	
	public function getAddToCartPostParams(\Magento\Catalog\Model\Product $product)
    {
        $url = $this->getAddToCartUrl($product);
        return [
            'action' => $url,
            'data' => [
                'product' => $product->getEntityId(),
                \Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED =>
                    $this->urlHelper->getEncodedUrl($url),
            ]
        ];
    }

	
	/**
     * Get form key
     *
     * @return string
     */
    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }
	
	
}

