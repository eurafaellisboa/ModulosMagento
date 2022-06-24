<?php
/**
 * Application configuration object. Used to access configuration when application is initialized and installed.
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MGS\ThemeSettings\Model\App;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
/**
 * Class Config
 */
class Config
{
	/**
	 * @var \Magento\Framework\Filesystem
	 */
    protected $_filesystem;
	
	/**
	 * @var \Magento\Framework\Xml\Parser
	 */
	protected $_parser;
	
	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $_storeManager;

    /**
     * Config constructor.
     *
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Xml\Parser $parser
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
		\Magento\Framework\Filesystem $filesystem,
		\Magento\Framework\Xml\Parser $parser,
		\Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
		$this->_filesystem = $filesystem;
		$this->_parser = $parser;
		$this->_storeManager = $storeManager;
    }

	
	public function aroundGetValue(\Magento\Framework\App\Config $subject, callable $proceed, $path)
	{
		$result = $proceed($path, 'stores', $this->_storeManager->getStore()->getId());
		
		if(isset($_SESSION["default"]["theme_customize"]) && $_SESSION["default"]["theme_customize"]){
			$tempType = $proceed('themesettings/customie_theme/temp_type');
			$settingPath = str_replace('/','_',$path);
			if($tempType == 'file'){
				$filePath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('mgs/customize/'.$this->_storeManager->getStore()->getId().'/settings.xml');
				if (is_readable($filePath)){
					$parsedArray = $this->_parser->load($filePath)->xmlToArray();
					if(isset($parsedArray['settings'][$settingPath])){
						$result = $parsedArray['settings'][$settingPath]['value'];
					}
				}
			}else{
				if(isset($_SESSION["default"]["session_setting"])){
					$sessionSetting = $_SESSION["default"]["session_setting"];
				
					if(isset($sessionSetting[$this->_storeManager->getStore()->getId()][$settingPath])){
						$result = $sessionSetting[$this->_storeManager->getStore()->getId()][$settingPath]['value'];
					}
				}
				
			}
			
		}

		return $result;
	}
}
