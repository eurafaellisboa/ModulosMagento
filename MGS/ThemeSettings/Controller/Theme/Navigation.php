<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
 
namespace MGS\ThemeSettings\Controller\Theme;
use Magento\Framework\Controller\ResultFactory;
class Navigation extends \MGS\ThemeSettings\Controller\Theme
{
	/**
     * @var Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;
	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\MGS\Fbuilder\Helper\Data $builderHelper,
		\Magento\Framework\App\Cache\Manager $cacheManager,
		\Magento\Framework\Filesystem $filesystem,
		\Magento\Framework\Xml\Parser $parser,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\MGS\ThemeSettings\Helper\Config $themeSettingConfig,
		\Magento\Framework\Filesystem\Io\File $ioFile,
		\Magento\Framework\Session\SessionManagerInterface $session,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory
	)     
	{
		parent::__construct($context, $builderHelper, $cacheManager, $filesystem, $parser, $storeManager, $themeSettingConfig, $ioFile, $session);
		$this->_resultPageFactory = $resultPageFactory;
	}
	
    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
		if($this->getRequest()->isAjax() && $this->builderHelper->showButton() && $this->session->getThemeCustomize()){
			$resultPage = $this->_resultPageFactory->create();
			$html = $resultPage->getLayout()
				->createBlock('MGS\ThemeSettings\Block\Customize\Edit')
				->setTemplate('MGS_ThemeSettings::customize/navigation.phtml')
				->toHtml();
			return $this->getResponse()->setBody($html);
		}
    }
}
