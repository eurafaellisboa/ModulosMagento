<?php

namespace Blueskytechco\PageBuilderCustom\Model\BlogPosts\Source;

class SortOrder implements \Magento\Framework\Option\ArrayInterface
{
    protected $options;

    public function toOptionArray()
    {
        if ($this->options === null) {
            $arr = ['newest' => __('Newest posts first'), 'oldest' => __('Oldest posts first')];

            foreach ($arr as $key => $item) {
                $this->options[] = [
                    'label' => $item,
                    'value' => $key,
                ];
            }
        }

        return $this->options;
    }

    public function toArray()
    {
        $array = [];
        foreach ($this->toOptionArray() as $item) {
            $array[$item['value']] = $item['label'];
        }
        return $array;
    }
}
