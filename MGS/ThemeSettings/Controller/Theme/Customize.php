<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
 
namespace MGS\ThemeSettings\Controller\Theme;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Cache\Manager as CacheManager;

class Customize extends \Magento\Framework\App\Action\Action
{
	protected $session;
	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		CustomerSession $customerSession,
		\MGS\Fbuilder\Helper\Data $builderHelper,
		\Magento\Framework\Session\SessionManagerInterface $session,
		CacheManager $cacheManager
	)     
	{
		$this->customerSession = $customerSession;
		$this->builderHelper = $builderHelper;
		$this->cacheManager = $cacheManager;
		$this->session = $session;
		parent::__construct($context);
	}
    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
		if($this->customerSession->getUseFrontendBuilder()==1){
			$this->customerSession->setUseFrontendBuilder(false);
			$this->customerSession->setBlockCopied(false);
			$this->cacheManager->clean(['full_page']);
		}
		if($this->builderHelper->showButton() && ($this->customerSession->getUseFrontendBuilder() != 1)){
			if(!$this->session->getThemeCustomize()){
				$this->session->setThemeCustomize(true);
			}
			
			$this->_view->loadLayout();
			$this->_view->getPage()->getConfig()->getTitle()->set(__('MGS Theme Setting - Customize'));
			$this->_view->renderLayout();
		}else{
			$this->session->setThemeCustomize(false);
			$this->session->setSessionSetting(false);
			$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
			$resultRedirect->setPath('');
			return $resultRedirect;
		}
    }
}
