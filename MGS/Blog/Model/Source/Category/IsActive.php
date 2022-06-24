<?php

namespace MGS\Blog\Model\Source\Category;

use Magento\Framework\Data\OptionSourceInterface;

class IsActive implements OptionSourceInterface
{
    protected $categoryModel;

    public function __construct(\MGS\Blog\Model\Category $categoryModel)
    {
        $this->categoryModel = $categoryModel;
    }

    public function toOptionArray()
    {
        $options[] = ['label' => '', 'value' => ''];
        $availableOptions = $this->categoryModel->getAvailableStatuses();

        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }

        return $options;
    }
}
