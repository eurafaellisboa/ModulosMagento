<?php
/**
 * Copyright © 2019 Blueskytechco. All rights reserved.
 */

namespace Blueskytechco\StoreLocator\Controller\Adminhtml;

use \Magento\Backend\App\Action;
use \Magento\Ui\Component\MassAction\Filter;
use \Blueskytechco\StoreLocator\Model\ResourceModel\Store\CollectionFactory as StoreCollectionFactory;
use \Blueskytechco\StoreLocator\Api\StoreRepositoryInterface;

abstract class MassAction extends Action
{
    protected $filter; 
    protected $storeCollectionFactory;
    protected $storeRepository;


    public function __construct(
        Action\Context $context,
        Filter $filter,
        StoreCollectionFactory $storeCollectionFactory,
        StoreRepositoryInterface $storeRepository
    ) {
        $this->filter = $filter;
        $this->storeRepository = $storeRepository;
        $this->categoryRepository= $categoryRepository;
        parent::__construct($context);
    }
}
