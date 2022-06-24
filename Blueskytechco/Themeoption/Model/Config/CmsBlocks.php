<?php
declare(strict_types=1);

namespace Blueskytechco\Themeoption\Model\Config;

use Magento\Cms\Model\ResourceModel\Block\CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

class CmsBlocks implements OptionSourceInterface
{
    private $options;
    private $collectionFactory;

    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    public function toOptionArray()
    {
        if (!$this->options) {
            $getoptions = $this->collectionFactory->create()->toOptionIdArray();
            $options[] = ['value' => '', 'label' => __('Select Blocks')];
            foreach ($getoptions as $key => $value) {
                $options[] = $value;
            }
            $this->options = $options;
        }
        
        return $this->options;
    }
}