<?php
declare(strict_types=1);

namespace Blueskytechco\PageBuilderCustom\Model\Config\Testimonial;

class TestimonialItems implements \Magento\Framework\Data\OptionSourceInterface
{
    private $_testimonialCollectionFactory;

    public function __construct(
        \Blueskytechco\Testimonial\Model\ResourceModel\Testimonial\CollectionFactory  $testimonialCollectionFactory
    ) {
        $this->_testimonialCollectionFactory = $testimonialCollectionFactory;
    }

    public function toOptionArray(): array
    {
        $options = [];
        $collection = $this->_testimonialCollectionFactory->create();

        foreach ($collection as $item) {
            $options[] = [
                'label' => $item->getName(),
                'value' => $item->getId(),
            ];
        }
        return $options;
    }
}