<?php

declare(strict_types=1);

namespace Blueskytechco\PageBuilderCustom\Plugin\Catalog\Block\Product;

use Magento\PageBuilder\Model\Catalog\Sorting;
use Magento\Catalog\Model\Category;
use Magento\CatalogInventory\Helper\Stock;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class DailyDeal
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
        \Blueskytechco\DailyDeal\Block\Widget\DailyDeal $subject,
        \Magento\Catalog\Model\ResourceModel\Product\Collection $result
    ) {
        $categoryId = $subject->getData('category_id');
        $this->stock->addIsInStockFilterToCollection($result);
        if (!empty($categoryId)) {
            try {
                $category = $this->categoryRepository->get($categoryId);
            } catch (NoSuchEntityException $noEntityException) {
                $category = null;
            }
            if ($category) {
                $result->addCategoryFilter($category);
            }
        }
        
        return $result;
    }

    public function afterGetCacheKeyInfo(\Blueskytechco\DailyDeal\Block\Widget\DailyDeal $subject, array $cacheKeys)
    {
        $cacheKeys[] = md5($subject->getData('category_id'));
        return $cacheKeys;
    }

    public function afterGetIdentities(\Blueskytechco\DailyDeal\Block\Widget\DailyDeal $subject, array $result)
    {
        $categoryId = $subject->getData('category_id');

        if (!empty($categoryId)) {
            $result[] = Category::CACHE_TAG . '_' . $categoryId;
        }
        return $result;
    }
}