<?php
/**
 * Copyright © 2021 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Blueskytechco\SearchSuite\Model;

use \Blueskytechco\SearchSuite\Model\SearchFactory;

/**
 * Search class returns needed search data
 */
class Search
{

    /**
     * @var \Blueskytechco\SearchSuite\Model\SearchFactory
     */
    protected $searchFactory;

    /**
     * Search constructor.
     *
     * @param \Blueskytechco\SearchSuite\Model\SearchFactory $searchFactory
     */
    public function __construct(
        SearchFactory $searchFactory
    ) {
        $this->searchFactory = $searchFactory;
    }

    /**
     * Retrieve suggested, product data
     *
     * @return array
     */
    public function getData()
    {
        $data               = [];
        $data['product'] = $this->searchFactory->create('product')->getResponseData();
        return $data;
    }
    
    /**
     * Retrieve suggested, product data
     *
     * @return array
     */
    public function getDataSuggestProduct()
    {
        $data               = [];
        $data['product'] = $this->searchFactory->create('product')->getResponseDataSuggestProduct();
        return $data;
    }
}
