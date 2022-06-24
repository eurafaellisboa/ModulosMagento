<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
 
namespace MGS\ThemeSettings\Controller\Theme;
use Magento\Framework\Controller\ResultFactory;
class Close extends \MGS\ThemeSettings\Controller\Theme
{
    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
		$this->session->setThemeCustomize(false);
		$this->session->setStyleInline(false);
		$this->session->setSessionSetting(false);
		$this->refreshCaches(['full_page']);
		if(!$this->getRequest()->isAjax()){
			$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
			$resultRedirect->setPath('');
			return $resultRedirect;
		}
    }
}
