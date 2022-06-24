<?php
declare(strict_types=1);

namespace Blueskytechco\Themeoption\Model\Config;

use Magento\Framework\Data\OptionSourceInterface;

class Googlefonts implements OptionSourceInterface
{
    private $options;
    private $_collectionFactory;

    public function __construct(
        \Blueskytechco\Themeoption\Model\ResourceModel\Googlefonts\CollectionFactory $collectionFactory
    ) {
        $this->_collectionFactory = $collectionFactory;
    }

    public function toOptionArray()
    {
        if (!$this->options) {
            $getoptions = $this->_collectionFactory->create();
            $options = [];
            foreach ($getoptions as $item) {
                $options[] = array( 'value' => $item->getFamily(), 'label' => $item->getFamily());
            }
            $this->options = $options;
        }
        
        return $this->options;
    }
}