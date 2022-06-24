<?php

declare(strict_types=1);

namespace Blueskytechco\PageBuilderCustom\Plugin\Catalog\Block\Product;

use Magento\PageBuilder\Model\Catalog\Sorting;
use Magento\Catalog\Model\Category;
use Magento\CatalogInventory\Helper\Stock;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class ProductsListPlugin
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
        \Blueskytechco\ProductWidgetAdvanced\Block\Widget\ProductAdvanced $subject,
        \Magento\Catalog\Model\ResourceModel\Product\Collection $result
    ) {
        $categoryId = $subject->getData('category_ids');
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

    public function afterGetCacheKeyInfo(\Blueskytechco\ProductWidgetAdvanced\Block\Widget\ProductAdvanced $subject, array $cacheKeys)
    {
        $cacheKeys[] = md5($subject->getData('category_ids').$subject->getData('product_type'));
        return $cacheKeys;
    }

    public function afterGetIdentities(\Blueskytechco\ProductWidgetAdvanced\Block\Widget\ProductAdvanced $subject, array $result)
    {
        $categoryId = $subject->getData('category_ids');

        if (!empty($categoryId)) {
            $result[] = Category::CACHE_TAG . '_' . $categoryId;
        }
        return $result;
    }
}