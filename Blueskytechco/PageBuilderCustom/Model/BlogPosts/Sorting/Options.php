<?php
declare(strict_types=1);

namespace Blueskytechco\PageBuilderCustom\Model\BlogPosts\Sorting;

class Options implements \Magento\Framework\Data\OptionSourceInterface
{

    public function toOptionArray(): array
    {
        $arr = ['' => __('Please select'), 'newest' => __('Newest posts first'), 'oldest' => __('Oldest posts first')];
        $options = [];
        foreach ($arr as $key => $option) {
            $options[] =
                [
                    'value' => $key,
                    'label' => $option
                ];
        }
        return $options;
    }
}
