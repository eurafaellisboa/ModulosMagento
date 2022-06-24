<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2018-03-02 09:13:16
 * @@Modify Date: 2018-06-17 17:20:28
 * @@Function:
 */

namespace Magiccart\Testimonial\Block\Post\PostList;

use Magento\Store\Model\ScopeInterface;

/**
 * Abstract blog post list block
 */
abstract class AbstractList extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    /**
     * @var \Magento\Cms\Model\Page
     */
    protected $_post;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magiccart\Testimonial\Model\ResourceModel\Testimonial\CollectionFactory
     */
    protected $_postCollectionFactory;

    /**
     * @var \Magiccart\Testimonial\Model\ResourceModel\Testimonial\Collection
     */
    protected $_postCollection;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Magiccart\Testimonial\Model\ResourceModel\Post\CollectionFactory $postCollectionFactory
     * @param \Magiccart\Testimonial\Model\Url $url
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magiccart\Testimonial\Model\ResourceModel\Testimonial\CollectionFactory $postCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_coreRegistry = $coreRegistry;
        $this->_filterProvider = $filterProvider;
        $this->_postCollectionFactory = $postCollectionFactory;
        $this->_url = $context->getUrlBuilder();
    }

    /**
     * Prepare posts collection
     *
     * @return void
     */
    protected function _preparePostCollection()
    {
        $store = $this->_storeManager->getStore()->getStoreId();
        $this->_postCollection = $this->_postCollectionFactory->create()
            ->addFieldToFilter('stores',array( array('finset' => 0), array('finset' => $store)))
            ->addFieldToFilter('status', 1)
            ->setOrder('testimonial_id', 'DESC');

        if ($this->getPageSize()) {
            $this->_postCollection->setPageSize($this->getPageSize());
        }
        $this->_postCollection->setCurPage($this->getCurrentPage());
    }

    /**
     * Prepare posts collection
     *
     * @return \Magiccart\Testimonial\Model\ResourceModel\Testimonial\Collection
     */
    public function getPostCollection()
    {
        if (null === $this->_postCollection) {
            $this->_preparePostCollection();
        }
        return $this->_postCollection;
    }

    public function getCurrentPage()
    {
        $page = (int) $this->getRequest()->getParam('p', 1);
        return $page ? $page : 1;
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->_scopeConfig->getValue(
            'testimonial/general/enabled',
            ScopeInterface::SCOPE_STORE
        )) {
            return '';
        }

        return parent::_toHtml();
    }
}
