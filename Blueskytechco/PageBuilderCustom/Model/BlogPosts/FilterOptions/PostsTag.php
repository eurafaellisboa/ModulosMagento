<?php
declare(strict_types=1);

namespace Blueskytechco\PageBuilderCustom\Model\BlogPosts\FilterOptions;

class PostsTag implements \Magento\Framework\Data\OptionSourceInterface
{
    private $_tagCollectionFactory;

    public function __construct(
        \Magefan\Blog\Model\ResourceModel\Tag\CollectionFactory $tagCollectionFactory
    ) {
        $this->_tagCollectionFactory = $tagCollectionFactory;
    }

    public function toOptionArray(): array
    {
        $options = [];
        $options[] = [
            'label' => __('Please select'),
            'value' => '0',
        ];
        $collection = $this->_tagCollectionFactory->create();
        $collection->setOrder('title');

        foreach ($collection as $item) {
            $options[] = [
                'label' => $item->getTitle(),
                'value' => $item->getId(),
            ];
        }
        return $options;
    }
}
