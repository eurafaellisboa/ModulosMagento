<?php
/**
 * Copyright © 2021 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Blueskytechco\MenuBuilder\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\RawFactory;
use Blueskytechco\MenuBuilder\Model\MenuBuilderFactory;
use Blueskytechco\MenuBuilder\Model\MenuBuilderItemFactory;
use Blueskytechco\MenuBuilder\Model\MenuBuilderItemMetaFactory;
use Blueskytechco\MenuBuilder\Model\ResourceModel\MenuBuilder\CollectionFactory;
use Blueskytechco\MenuBuilder\Model\ResourceModel\MenuBuilderItemMeta\CollectionFactory as MenuItemMetaCollectionFactory;
use Blueskytechco\MenuBuilder\Helper\Admin as MenuHelper;
use Psr\Log\LoggerInterface;
use Magento\Framework\Json\Helper\Data;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Controller\Result\JsonFactory;

abstract class AbstractMenu extends Action
{
    const ADMIN_RESOURCE = 'Blueskytechco_MenuBuilder::menus_builder';

    /**
     * @var PageFactory
     */
    public $resultPageFactory;

    /**
     * @var RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Data
     */
    public $jsonHelper;
    
    /**
     * @var MenuHelper
     */
    public $menuHelper;

    /**
     * @var LoggerInterface
     */
    public $logger;

    /**
     * @var MenuBuilderFactory
     */
    protected $menuBuilderFactory;
    
    /**
     * @var MenuBuilderItemFactory
     */
    protected $menuBuilderItemFactory;
    
    /**
     * @var MenuBuilderItemMetaFactory
     */
    protected $menuBuilderItemMetaFactory;
    
    /**
     * @var CollectionFactory
     */
    protected $collectionMenuBuilderFactory;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * Massactions filter
     *
     * @var Filter
     */
    protected $filter;

    /**
     * @var \Magento\Framework\Component\ComponentRegistrar
     */
    protected $componentRegistrar;
    
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlInterface;

    /**
     * @var \Magento\Framework\Xml\Parser
     */
    protected $_parser;
    
    public function __construct(
        Context $context,
        MenuBuilderFactory $menuBuilderFactory,
        MenuBuilderItemFactory $menuBuilderItemFactory,
        MenuBuilderItemMetaFactory $menuBuilderItemMetaFactory,
        CollectionFactory $collectionMenuBuilderFactory,
        MenuItemMetaCollectionFactory $collectionBuilderItemMetaFactory,
        PageFactory $resultPageFactory,
        RawFactory $resultRawFactory,
        LoggerInterface $logger,
        Data $jsonHelper,
        MenuHelper $menuHelper,
        Registry $registry,
        DateTime $dateTime,
        Filter $filter,
        ComponentRegistrar $componentRegistrar,
        \Magento\Framework\UrlInterface $urlInterface,
        JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->resultRawFactory = $resultRawFactory;
        $this->menuBuilderFactory = $menuBuilderFactory;
        $this->menuBuilderItemFactory = $menuBuilderItemFactory;
        $this->menuBuilderItemMetaFactory = $menuBuilderItemMetaFactory;
        $this->collectionMenuBuilderFactory = $collectionMenuBuilderFactory;
        $this->collectionBuilderItemMetaFactory = $collectionBuilderItemMetaFactory;
        $this->menuHelper = $menuHelper;
        $this->logger = $logger;
        $this->jsonHelper = $jsonHelper;
        $this->registry = $registry;
        $this->dateTime = $dateTime;
        $this->filter = $filter;
        $this->_parser = new \Magento\Framework\Xml\Parser();
        $this->componentRegistrar = $componentRegistrar;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_urlInterface = $urlInterface;
    }
}
