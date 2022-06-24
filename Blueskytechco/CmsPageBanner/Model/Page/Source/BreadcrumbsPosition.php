<?php

namespace Blueskytechco\CmsPageBanner\Model\Page\Source;

use Magento\Framework\Data\OptionSourceInterface;

class BreadcrumbsPosition implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [['label' => __('After Content Heading'), 'value' => 'after'], ['label' => __('Before Content Heading'), 'value' => 'before']];
    }
}
