<?php

declare(strict_types=1);

namespace Blueskytechco\PageBuilderCustom\Ui\DataProvider\Product;

use Magento\Store\Model\Store;

class ProductAdvancedCollection extends \Magento\Catalog\Model\ResourceModel\Product\Collection
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
        $conditions = [
            'cat_pro.product_id=e.entity_id',
            $this->getConnection()->quoteInto(
                'cat_pro.category_id = ?',
                $this->_productLimitationFilters['category_id'],
                \Zend_Db::INT_TYPE
            ),
        ];
        $joinCond = join(' AND ', $conditions);
        $fromPart = $this->getSelect()->getPart(\Magento\Framework\DB\Select::FROM);
        if (isset($fromPart['cat_pro'])) {
            $fromPart['cat_pro']['joinCondition'] = $joinCond;
            $this->getSelect()->setPart(\Magento\Framework\DB\Select::FROM, $fromPart);
        } else {
            $this->getSelect()->joinLeft(
                ['cat_pro' => $this->getTable('catalog_category_product')],
                $joinCond,
                ['cat_index_position' => $this->getConnection()->getIfNullSql('position', '~0')]
            );
        }
        $this->_joinFields['position'] = ['table' => 'cat_pro', 'field' => 'position'];

        return $this;
    }
}