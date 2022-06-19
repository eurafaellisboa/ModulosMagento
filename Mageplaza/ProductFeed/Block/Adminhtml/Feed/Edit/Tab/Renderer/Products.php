<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_ProductFeed
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ProductFeed\Block\Adminhtml\Feed\Edit\Tab\Renderer;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Mageplaza\ProductFeed\Model\FeedFactory;

/**
 * Class Products
 * @package Mageplaza\ProductFeed\Block\Adminhtml\Feed\Edit\Tab\Renderer
 */
class Products extends Extended
{
    /**
     * @var CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var FeedFactory
     */
    protected $_feedFactory;

    /**
     * Products constructor.
     * @param Context $context
     * @param Data $backendHelper
     * @param FeedFactory $feedFactory
     * @param CollectionFactory $productCollectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        FeedFactory $feedFactory,
        CollectionFactory $productCollectionFactory,
        array $data = []
    )
    {
        parent::__construct($context, $backendHelper, $data);

        $this->_feedFactory = $feedFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
    }

    /**
     * _construct
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('productsGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(false);
        $this->setUseAjax(true);
    }

    /**
     * prepare collection
     */
    protected function _prepareCollection()
    {
        $collection = $this->_productCollectionFactory->create()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('price')
            ->addIdFilter($this->_getSelectedProducts());
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return Extended
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', [
            'header'           => __('Product ID'),
            'type'             => 'number',
            'index'            => 'entity_id',
            'header_css_class' => 'col-id',
            'column_css_class' => 'col-id',
        ]);
        $this->addColumn('name', [
            'header' => __('Name'),
            'index'  => 'name',
            'class'  => 'xxx',
            'width'  => '50px',
        ]);
        $this->addColumn('sku', [
            'header' => __('Sku'),
            'index'  => 'sku',
            'class'  => 'xxx',
            'width'  => '50px',
        ]);
        $this->addColumn('price', [
            'header' => __('Price'),
            'type'   => 'currency',
            'index'  => 'price',
            'width'  => '50px',
        ]);

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/products');
    }

    /**
     * @param  object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return '';
    }

    /**
     * @return mixed
     */
    protected function _getSelectedProducts()
    {
        $feedIds = $this->_feedFactory->create()->getMatchingProductIds();

        return $feedIds;
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return true;
    }
}
