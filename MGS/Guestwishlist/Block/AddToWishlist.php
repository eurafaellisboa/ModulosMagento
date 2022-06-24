<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MGS\Guestwishlist\Block;

/**
 * Wishlist js plugin initialization block
 */
class AddToWishlist extends \Magento\Framework\View\Element\Template
{
    /**
     * Product types
     *
     * @var array|null
     */
    private $productTypes;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * Returns wishlist widget options
     *
     * @return array
     */
    public function getWishlistOptions($productCollection)
    {
        return ['productType' => $this->getProductTypes($productCollection)];
    }

    /**
     * Returns an array of product types
     *
     * @return array|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getProductTypes($productCollection)
    {
        if ($this->productTypes === null) {
            $this->productTypes = [];
            if ($productCollection) {
                /** @var $product \Magento\Catalog\Model\Product */
                foreach ($productCollection as $product) {
                    $productTypes[] = $product->getTypeId();
                }
                $this->productTypes = array_unique($productTypes);
            }
        }
        return $this->productTypes;
    }
}
