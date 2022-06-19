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

namespace Mageplaza\ProductFeed\Block\Adminhtml\Feed\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Helper\Data;
use Magento\Framework\Registry;
use Mageplaza\ProductFeed\Model\ResourceModel\History\CollectionFactory;

/**
 * Class History
 * @package Mageplaza\ProductFeed\Block\Adminhtml\Feed\Edit\Tab
 */
class History extends Extended implements TabInterface
{
    /**
     * @var CollectionFactory
     */
    protected $historyCollectionFactory;

    /**
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * History constructor.
     * @param Context $context
     * @param Registry $coreRegistry
     * @param Data $backendHelper
     * @param CollectionFactory $historyCollectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        Data $backendHelper,
        CollectionFactory $historyCollectionFactory,
        array $data = []
    )
    {
        parent::__construct($context, $backendHelper, $data);

        $this->coreRegistry = $coreRegistry;
        $this->historyCollectionFactory = $historyCollectionFactory;
    }

    /**
     * Set grid params
     */
    public function _construct()
    {
        parent::_construct();

        $this->setId('history_grid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(false);
        $this->setUseAjax(true);
    }

    /**
     * @inheritdoc
     */
    protected function _prepareCollection()
    {
        $feed = $this->getFeed();
        $collection = $this->historyCollectionFactory->create();
        if ($feed && $feed->getId()) {
            $collection = $collection->addFieldToFilter('feed_id', $feed->getId());
        }

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('id', [
            'header'           => __('ID'),
            'sortable'         => true,
            'index'            => 'id',
            'type'             => 'number',
            'header_css_class' => 'col-id',
            'column_css_class' => 'col-id'
        ]);
        $this->addColumn('created_at', [
            'header'           => __('Generation time'),
            'index'            => 'created_at',
            'header_css_class' => 'col-name',
            'column_css_class' => 'col-name'
        ]);
        $this->addColumn('type', [
            'header' => __('Type'),
            'name'   => 'type',
            'index'  => 'type',
        ]);
        $this->addColumn('file', [
            'header' => __('File'),
            'name'   => 'file',
            'index'  => 'file',
        ]);
        $this->addColumn('status', [
            'header' => __('Status'),
            'name'   => 'status',
            'index'  => 'status',
        ]);
        $this->addColumn('success_message', [
            'header' => __('Success Message'),
            'name'   => 'success_message',
            'index'  => 'success_message',
        ]);
        $this->addColumn('delivery', [
            'header' => __('Delivery'),
            'name'   => 'delivery',
            'index'  => 'delivery',
        ]);
        $this->addColumn('error_message', [
            'header' => __('Error Message'),
            'name'   => 'error_message',
            'index'  => 'error_message',
        ]);

        return $this;
    }

    /**
     * get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/log', ['feed_id' => $this->getFeed()->getFeedId()]);
    }

    /**
     * @return \Mageplaza\ProductFeed\Model\Feed
     */
    public function getFeed()
    {
        return $this->coreRegistry->registry('mageplaza_productfeed_feed');
    }

    /**
     * @return string
     */
    public function getTabLabel()
    {
        return __('Logs');
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return string
     */
    public function getTabUrl()
    {
        return $this->getUrl('mpproductfeed/logs/log', ['_current' => true]);
    }

    /**
     * @return string
     */
    public function getTabClass()
    {
        return 'ajax only';
    }
}
