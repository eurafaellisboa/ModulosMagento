<?php

declare(strict_types=1);

namespace Blueskytechco\PageBuilderCustom\Ui\DataProvider\Product;

use Magento\Store\Model\Store;

class ProductPriceCountdownCollection extends \Magento\Catalog\Model\ResourceModel\Product\Collection
{

    public function setVisibility($visibility)
    {
        if ($this->getStoreId() === Store::DEFAULT_STORE_ID) {
            $this->addAttributeToFilter('visibility', $visibility);
        } else {
            parent::setVisibility($visibility);
        }

        return $this;
    }

    protected function _productLimitationJoinPrice()
    {
        $this->_productLimitationFilters->setUsePriceIndex($this->getStoreId() !== Store::DEFAULT_STORE_ID);
        return $this->_productLimitationPrice(false);
    }

    protected function _applyZeroStoreProductLimitations()
    {
        return $this;
    }
}