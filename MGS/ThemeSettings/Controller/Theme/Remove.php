<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
 
namespace MGS\ThemeSettings\Controller\Theme;
use Magento\Framework\Controller\ResultFactory;
class Remove extends \MGS\ThemeSettings\Controller\Theme
{
    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
		if($this->getRequest()->isAjax()){
			if($this->builderHelper->showButton() && $this->session->getThemeCustomize()){
				$settingPath = $this->getRequest()->getParam('id');
				$settingValue = '';
				
				$this->generateSettingTemp($settingPath, $settingValue);
				if($this->getRequest()->getParam('style')){
					$styleInline = $this->themeSetting->getStyleInline($this->_storeManager->getStore()->getId());
					$this->session->setStyleInline($styleInline);
					return $this->getResponse()->setBody($styleInline);
				}
			}
		}else{
			$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
			$resultRedirect->setPath('');
			return $resultRedirect;
		}
    }
}
