<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
 
namespace MGS\ThemeSettings\Controller\Theme;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
class Savesetting extends \MGS\ThemeSettings\Controller\Theme
{
	
	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\MGS\Fbuilder\Helper\Data $builderHelper,
		\Magento\Framework\App\Cache\Manager $cacheManager,
		\Magento\Framework\Filesystem $filesystem,
		\Magento\Framework\Xml\Parser $parser,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\MGS\ThemeSettings\Helper\Config $themeSettingConfig,
		\MGS\Fbuilder\Helper\Generate $builderGenerateHelper,
		\Magento\Framework\Filesystem\Io\File $ioFile,
		\Magento\Framework\Session\SessionManagerInterface $session,
		WriterInterface $configWriter
	)     
	{
		$this->configWriter = $configWriter;
		$this->builderGenerateHelper = $builderGenerateHelper;
		parent::__construct($context, $builderHelper, $cacheManager, $filesystem, $parser, $storeManager, $themeSettingConfig, $ioFile, $session);
	}
	
    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
		if($this->builderHelper->showButton() && $this->session->getThemeCustomize()){
			$storeId = $this->_storeManager->getStore()->getId();
			$scope = 'stores';
			$scopeId = $storeId;
			if($this->getRequest()->getParam('scope')){
				$scope = 'default';
				$scopeId = 0;
			}
			
			if($this->themeSetting->getStoreConfig('themesettings/customie_theme/temp_type')=='file'){
				
				$filePath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('mgs/customize/'.$storeId.'/settings.xml');
				
				if (is_readable($filePath)){
					try{
						$tempSetting = $this->_parser->load($filePath)->xmlToArray();
						foreach($tempSetting['settings'] as $configPath => $value){
							$this->configWriter->save($value['path'], $value['value'], $scope, $scopeId);
						}
						
						if($scope=='default'){
							$this->builderGenerateHelper->generateCss();
						}else{
							$this->builderGenerateHelper->generateCssByStore($scopeId);
						}
						
						
						$this->refreshCaches(['full_page', 'config']);
						$this->messageManager->addSuccess(
							__('You saved the configuration.')
						);	
					}catch(Exception $e){
						$this->messageManager->addError(
							$e->getMessage()
						);
					}
				}else{
					$this->messageManager->addError(
						__('We can\'t process your request right now.')
					);
				}
			}else{
				$sessionSetting = $this->session->getSessionSetting();
				if($sessionSetting && isset($sessionSetting[$storeId])){
					try{
						foreach($sessionSetting[$storeId] as $configPath => $value){
							$this->configWriter->save($value['path'], $value['value'], $scope, $scopeId);
						}
					}catch(Exception $e){
						$this->messageManager->addError(
							$e->getMessage()
						);
					}
				}else{
					$this->messageManager->addError(
						__('We can\'t process your request right now.')
					);
				}
			}
			
			
			
		}
		$this->_redirect($this->_redirect->getRefererUrl());
		return;
    }
}
