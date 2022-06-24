<?php
/**
 * Copyright © 2019 Blueskytechco. All rights reserved.
 */

namespace Blueskytechco\StoreLocator\Model\System\Config;

use \Magento\Framework\Option\ArrayInterface;
use \Blueskytechco\StoreLocator\Model\Source\IsActive as Source;

class IsActive implements ArrayInterface
{
    /**
     * @var \Blueskytechco\StoreLocator\Model\Source\IsActive
     */
    private $source;


    public function __construct(Source $source)
    {
        $this->source = $source;
    }

    public function toOptionArray()
    {
        return $this->source->getAvailableStatuses();
    }
}
