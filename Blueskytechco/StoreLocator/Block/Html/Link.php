<?php
/**
 * Copyright © 2019 Blueskytechco. All rights reserved.
 */

namespace Blueskytechco\StoreLocator\Block\Html;


class Link extends \Magento\Framework\View\Element\Html\Link
{

    public function getHref()
    {
        return $this->_urlBuilder->getUrl('storelocator');
    }

    public function isCurrent()
    {
        return $this->getData('current') || $this->_urlBuilder->getCurrentUrl() === $this->getHref();
    }
}
