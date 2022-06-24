<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Blueskytechco\AjaxSuite\Block\AjaxCart;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Category;

/**
 * Product View block
 * @api
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @since 100.0.2
 */
class Success extends \Magento\Catalog\Block\Product\View
{
    public function getQuoteItem()
    {
        return $this->_coreRegistry->registry('quote_item');
    }
    public function getFormatedPrice($price)
    {
        return $this->priceCurrency->convertAndFormat($price);
    }
}
