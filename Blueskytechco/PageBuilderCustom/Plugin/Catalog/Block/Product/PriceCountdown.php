<?php

declare(strict_types=1);

namespace Blueskytechco\PageBuilderCustom\Plugin\Catalog\Block\Product;

use Magento\PageBuilder\Model\Catalog\Sorting;
use Magento\Catalog\Model\Category;
use Magento\CatalogInventory\Helper\Stock;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class PriceCountdown
{

    private $sorting;
    private $stock;
    private $categoryRepository;

    public function __construct(
        Sorting $sorting,
        Stock $stock,
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->sorting = $sorting;
        $this->stock = $stock;
        $this->categoryRepository = $categoryRepository;
    }

    public function afterCreateCollection(
        \Blueskytechco\PriceCountdown\Block\Widget\PriceCountdown $subject,
        \Magento\Catalog\Model\ResourceModel\Product\Collection $result
    ) {
        $this->stock->addIsInStockFilterToCollection($result);
        return $result;
    }
}