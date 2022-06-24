<?php
/**
 * Copyright © 2021 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
 
namespace Blueskytechco\SearchSuite\Model;

/**
 * @api
 */
interface SearchInterface
{
    /**
     * Retrieve selected in config data
     *
     * @return array
     */
    public function getResponseData();

}
