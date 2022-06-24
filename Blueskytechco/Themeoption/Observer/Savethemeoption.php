<?php

namespace Blueskytechco\Themeoption\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\PageCache\Version;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Cache\Frontend\Pool;

class Savethemeoption implements ObserverInterface
{
    protected $_css;
    protected $_themeoptionversion;
	protected $cacheTypeList;
	protected $cacheFrontendPool;
    
    public function __construct(
        \Blueskytechco\Themeoption\Model\Custom\Generator $css,
		TypeListInterface $cacheTypeList, 
		Pool $cacheFrontendPool,
        \Blueskytechco\Themeoption\Model\ThemeoptionversionFactory $themeoptionversion
    ) {
        $this->_css = $css;
        $this->_themeoptionversion = $themeoptionversion;
		$this->cacheTypeList = $cacheTypeList;
		$this->cacheFrontendPool = $cacheFrontendPool;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
		$t_option = $this->_themeoptionversion->create();
		$t_option->setVersion(strtotime('now'));
		$t_option->setVersionTime(date('Y-m-d H:i:s'));
		$t_option->save();
        $this->_css->generateCss($observer->getData("website"), $observer->getData("store"));
		
		$_types = [
            'config',
            'layout',
            'block_html',
            'full_page'
        ];
 
		foreach ($_types as $type) {
			$this->cacheTypeList->cleanType($type);
		}
		
		foreach ($this->cacheFrontendPool as $cacheFrontend) {
			$cacheFrontend->getBackend()->clean();
		}
    }
}
