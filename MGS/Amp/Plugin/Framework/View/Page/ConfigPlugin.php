<?php
namespace MGS\Amp\Plugin\Framework\View\Page;

use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Asset\GroupedCollection;

class ConfigPlugin {
    /**
     * @var \MGS\Amp\Helper\Config
     */
    protected $_configHelper;

    /**
     * @param \MGS\Amp\Helper\Config $configHelper
     * @return  void
     */
    public function __construct(
        \MGS\Amp\Helper\Config $configHelper
    ) {
        $this->_configHelper = $configHelper;
    }

    /**
     * @param  \Magento\Framework\View\Page\Config
     * @param  \Magento\Framework\View\Asset\GroupedCollection $result
     * @return \Magento\Framework\View\Asset\GroupedCollection $result
     */
    public function afterGetAssetCollection(Config $subject, $result)
    {
        if (!$this->_configHelper->isAmpCall()){
            return $result;
        }

        foreach ($result->getGroups() as $group) {
            $type = $group->getProperty(GroupedCollection::PROPERTY_CONTENT_TYPE);
            if (!in_array($type, ['canonical', 'ico'])) {
                foreach ($group->getAll() as $identifier => $asset) {
                    $result->remove($identifier);
                }
            }

            if ($type == 'canonical') {
                $assetsCollection = $group->getAll();

                if (!count($assetsCollection)) {
                    $subject->addRemotePageAsset(
                        $this->_configHelper->getCanonicalUrl(),
                        'canonical',
                        ['attributes' => ['rel' => 'canonical']]
                    );
                } else {
                    foreach ($assetsCollection as $identifier => $asset) {
                        if ($identifier != 'pramp-asset') {
                            $result->remove($identifier);
                        }
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @param  \Magento\Framework\View\Page\Config
     * @param  array $result
     * @param  string $elementType
     * @return array $result
     */
    public function aroundGetElementAttributes(Config $subject, \Closure $proceed, $elementType)
    {
        /**
         * Get result by original method
         */
        $result = $proceed($elementType);

        /**
         * Add attributes in tags by $elementType
         * (Only for amp request)
         */
        if ($this->_configHelper->isAmpCall()) {
            switch (strtolower($elementType)) {
                case \Magento\Framework\View\Page\Config::ELEMENT_TYPE_HTML:
                    $result['amp'] = '';
                    break;
                case \Magento\Framework\View\Page\Config::ELEMENT_TYPE_BODY:
                    $result = array_diff_key($result, array_count_values(['itemtype', 'itemscope', 'itemprop']));
                    break;
                default:
                    break;
            }

        }

        return $result;
    }
	
	public function aroundGetValue(\Magento\Framework\App\Config $subject, callable $proceed, $path)
	{
		echo $path; die();
		$result = $proceed($path, 'stores', $this->_storeManager->getStore()->getId());
		
		if (!$this->_configHelper->isAmpCall()){
            return $result;
        }

		return $result;
	}

}
