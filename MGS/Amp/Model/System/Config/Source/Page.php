<?php

namespace MGS\Amp\Model\System\Config\Source;

class Page implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'cms_index_index' => __('Home Page'),
            'cms_page_view' => __('CMS Pages'),
            'catalog_product_view' => __('Product Pages'),
            'catalog_category_view' => __('Category Pages'),
        ];
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $result = [];
        foreach ($this->toArray() as $value => $label) {
            $result[] = ['value' => $value, 'label' => $label ];
        }

        return $result;
    }
}