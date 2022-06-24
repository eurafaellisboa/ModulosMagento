<?php
declare(strict_types=1);

namespace Blueskytechco\PageBuilderCustom\Model\BlogPosts\FilterOptions;

class PostsAuthor implements \Magento\Framework\Data\OptionSourceInterface
{
    private $_authorCollectionFactory;

    public function __construct(
        \Magefan\Blog\Api\AuthorCollectionInterfaceFactory  $authorCollectionFactory
    ) {
        $this->_authorCollectionFactory = $authorCollectionFactory;
    }

    public function toOptionArray(): array
    {
        $options = [];
        $options[] = [
            'label' => __('Please select'),
            'value' => '0',
        ];
        $collection = $this->_authorCollectionFactory->create();

        foreach ($collection as $item) {
            $options[] = [
                'label' => $item->getName(),
                'value' => $item->getId(),
            ];
        }
        return $options;
    }
}
