<?php

namespace Digitaria\CustomUrlSitemap\Model\ItemProvider;

use Magento\Sitemap\Model\ItemProvider\ConfigReaderInterface;;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class ExamplePagesConfigReader
 * @package Digitaria\CustomUrlSitemap\Model\ItemProvider
 */
class ExamplePagesConfigReader implements ConfigReaderInterface
{
	const XML_PATH_CHANGE_FREQUENCY = 'sitemap/example_pages/changefreq';
	const XML_PATH_PRIORITY = 'sitemap/example_pages/priority';

	/**
 	* @var ScopeConfigInterface
 	*/
	private $scopeConfig;

	/**
 	* @param ScopeConfigInterface $scopeConfig
 	*
 	*/
	public function __construct(ScopeConfigInterface $scopeConfig)
	{
    	$this->scopeConfig = $scopeConfig;
	}

	/**
 	* @param int $storeId
 	* @return string
 	*/
	public function getPriority($storeId): string
	{
    	$storeId = (int)$storeId;
    	return $this->getConfigValue(self::XML_PATH_PRIORITY, $storeId);
	}

	/**
 	* @param int $storeId
 	* @return string
 	*/
	public function getChangeFrequency($storeId): string
	{
    	$storeId = (int)$storeId;
    	return $this->getConfigValue(self::XML_PATH_CHANGE_FREQUENCY, $storeId);
	}

	/**
 	* @param string $configPath
 	* @param int $storeId
 	*
 	* @return string
 	*
 	*/
	private function getConfigValue(string $configPath, int $storeId): string
	{
    	$configValue = $this->scopeConfig->getValue(
        	$configPath,
        	ScopeInterface::SCOPE_STORE,
        	$storeId
    	);
    	return (string)$configValue;
	}
}