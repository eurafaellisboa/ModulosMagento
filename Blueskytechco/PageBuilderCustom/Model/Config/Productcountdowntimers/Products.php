<?php
declare(strict_types=1);

namespace Blueskytechco\PageBuilderCustom\Model\Config\Productcountdowntimers;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\Product\Visibility;

class Products implements \Magento\Framework\Data\OptionSourceInterface
{
    private $catalogProductVisibility;
    private $productCollectionFactory;

    public function __construct(
        CollectionFactory $productCollectionFactory,
        Visibility $catalogProductVisibility
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->catalogProductVisibility = $catalogProductVisibility;
    }

    public function toOptionArray(): array
    {
        $options = [];
        $todayDate= date('Y-m-d');
        $collection = $this->productCollectionFactory->create();
        $collection->setStoreId(0);
        $collection->addFieldToSelect('name');
        $collection->addAttributeToSelect('special_from_date');
        $collection->addAttributeToSelect('special_to_date');
        $collection->addAttributeToFilter([
            [
                'attribute' => 'special_from_date',
                'lteq' => date('Y-m-d G:i:s', strtotime($todayDate)),
                'date' => true,
            ],
            [
                'attribute' => 'special_to_date',
                'gteq' => date('Y-m-d G:i:s', strtotime($todayDate)),
                'date' => true,
            ]
        ]);
        $collection->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());
        
        foreach ($collection as $item) {
            $options[] = [
                'label' => $item->getSku().' - '.$item->getName(),
                'value' => $item->getId(),
            ];
        }

        return $options;
    }
}