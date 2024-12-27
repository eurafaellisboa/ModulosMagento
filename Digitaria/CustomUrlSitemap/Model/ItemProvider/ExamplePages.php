<?php

namespace Digitaria\CustomUrlSitemap\Model\ItemProvider;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sitemap\Model\ItemProvider\ItemProviderInterface;
use Magento\Sitemap\Model\SitemapItemFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class ExamplePages
 * @package Digitaria\CustomUrlSitemap\Model\ItemProvider
 */
class ExamplePages implements ItemProviderInterface
{
    /**
     * @var ExamplePagesConfigReader
     */
    private $configReader;

    /**
     * @var SitemapItemFactory
     */
    private $itemFactory;

    /**
     * @var \Magento\Cms\Api\PageRepositoryInterface
     */
    private $pageRepositoryInterface;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var array
     */
    protected $sitemapItems = [];

    /**
     * ExamplePages constructor.
     * @param ExamplePagesConfigReader $configReader
     * @param SitemapItemFactory $itemFactory
     * @param \Magento\Cms\Api\PageRepositoryInterface $pageRepositoryInterface
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ExamplePagesConfigReader $configReader,
        SitemapItemFactory $itemFactory,
        \Magento\Cms\Api\PageRepositoryInterface $pageRepositoryInterface,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->configReader = $configReader;
        $this->itemFactory = $itemFactory;
        $this->pageRepositoryInterface = $pageRepositoryInterface;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param int $storeId
     * @return array
     * @throws NoSuchEntityException
     */
    public function getItems($storeId): array
    {
        // Retrieve CMS pages
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $pages = $this->pageRepositoryInterface->getList($searchCriteria)->getItems();

        foreach ($pages as $page) {
            $this->sitemapItems[] = $this->itemFactory->create(
                [
                    'url' => $page->getIdentifier(),
                    'updatedAt' => $page->getUpdateTime(),
                    'priority' => $this->getPriority($storeId),
                    'changeFrequency' => $this->getChangeFrequency($storeId)
                ]
            );
        }

        // Add custom URLs from admin configuration
        $customUrls = $this->getCustomUrls($storeId);
        foreach ($customUrls as $url) {
            $this->sitemapItems[] = $this->itemFactory->create(
                [
                    'url' => $url,
                    'updatedAt' => null,
                    'priority' => $this->getPriority($storeId),
                    'changeFrequency' => $this->getChangeFrequency($storeId)
                ]
            );
        }

        return $this->sitemapItems;
    }

    /**
     * Retrieve custom URLs from admin configuration
     * @param int $storeId
     * @return array
     */
    private function getCustomUrls(int $storeId): array
    {
        $customUrlsConfig = $this->scopeConfig->getValue(
            'sitemap/example_pages/custom_urls',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return array_filter(array_map('trim', explode("\n", $customUrlsConfig)));
    }

    /**
     * @param int $storeId
     * @return string
     */
    private function getChangeFrequency(int $storeId): string
    {
        return $this->configReader->getChangeFrequency($storeId);
    }

    /**
     * @param int $storeId
     * @return string
     */
    private function getPriority(int $storeId): string
    {
        return $this->configReader->getPriority($storeId);
    }
}
