<?php
declare(strict_types=1);

namespace Blueskytechco\PageBuilderCustom\Model\Config\Lookbook;

class LookBookItems implements \Magento\Framework\Data\OptionSourceInterface
{
    private $_lookCollectionFactory;

    public function __construct(
        \Blueskytechco\SetProduct\Model\ResourceModel\ProductSet\CollectionFactory  $lookCollectionFactory
    ) {
        $this->_lookCollectionFactory = $lookCollectionFactory;
    }

    public function toOptionArray(): array
    {
        $options = [];
        $collection = $this->_lookCollectionFactory->create();

        foreach ($collection as $item) {
            $options[] = [
                'label' => $item->getName(),
                'value' => $item->getIdentifier(),
            ];
        }
        return $options;
    }
}