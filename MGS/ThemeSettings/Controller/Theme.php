<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
 
namespace MGS\ThemeSettings\Controller;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Cache\Manager as CacheManager;
use Magento\Framework\App\Filesystem\DirectoryList;

abstract class Theme extends \Magento\Framework\App\Action\Action
{
	/**
     * @var \Magento\Framework\Filesystem
     */
	protected $_filesystem;
	
	protected $_ioFile;
	
	protected $_storeManager;
	
	/**
	 * @var \Magento\Framework\Xml\Parser
	 */
	protected $_parser;
	protected $themeSetting;
	protected $session;
	
	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\MGS\Fbuilder\Helper\Data $builderHelper,
		CacheManager $cacheManager,
		\Magento\Framework\Filesystem $filesystem,
		\Magento\Framework\Xml\Parser $parser,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\MGS\ThemeSettings\Helper\Config $themeSettingConfig,
		\Magento\Framework\Filesystem\Io\File $ioFile,
		\Magento\Framework\Session\SessionManagerInterface $session
	)     
	{
		parent::__construct($context);
		$this->builderHelper = $builderHelper;
		$this->themeSetting = $themeSettingConfig;
		$this->cacheManager = $cacheManager;
		$this->_filesystem = $filesystem;
		$this->_parser = $parser;
		$this->_storeManager = $storeManager;
		$this->_ioFile = $ioFile;
		$this->session = $session;
	}
	
	public function generateSettingTemp($settingPath, $settingValue){
		if($this->themeSetting->getStoreConfig('themesettings/customie_theme/temp_type')=='file'){
			$this->generateXmlFile($settingPath, $settingValue);
		}else{
			$this->generateSession($settingPath, $settingValue);
		}
		
		
	}
	
	public function generateXmlFile($settingPath, $settingValue){
		$filePath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('mgs/customize/'.$this->_storeManager->getStore()->getId().'/settings.xml');

		if (is_readable($filePath)){
			
			$tempSetting = $this->_parser->load($filePath)->xmlToArray();
			$tempSetting['settings'][$settingPath]['value'] = $settingValue;

			$html = "<settings>\n";
			foreach($tempSetting['settings'] as $path=>$value){
				$html .= "\t<".$path.">\n";
				$html .= "\t\t<path><![CDATA[".$value['path']."]]></path>\n";
				$html .= "\t\t<value><![CDATA[".$value['value']."]]></value>\n";
				$html .= "\t</".$path.">\n";
			}
			$html .= "</settings>";

			$io = $this->_ioFile;
			$io->setAllowCreateFolders(true);
			$io->open(array('path' => $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath($path)));
			$io->write($filePath, $html, 0644);
			$io->streamClose();

		}
	}
	
	public function generateSession($settingPath, $settingValue){
		$storeId = $this->_storeManager->getStore()->getId();
		$sessionSetting = $this->session->getSessionSetting();
		if($sessionSetting && isset($sessionSetting[$storeId])){
			$sessionSetting[$storeId][$settingPath]['value'] = $settingValue;
			$this->session->setSessionSetting($sessionSetting);
		}
	}
	
	public function redirectHome(){
		$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
		$resultRedirect->setPath('');
		return $resultRedirect;
	}
	
	public function refreshCaches($caches){
		$this->cacheManager->clean($caches);
	}
}
