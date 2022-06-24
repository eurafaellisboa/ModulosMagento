<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MGS\ThemeSettings\Helper;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Contact base helper
 */
class Fbuilder extends \MGS\Fbuilder\Helper\Data
{
	protected $session;
	public function __construct(
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\Stdlib\DateTime\DateTime $date,
		\Magento\Framework\ObjectManagerInterface $objectManager,
		\Magento\Framework\Url $url,
		\Magento\Framework\Filesystem $filesystem,
		\Magento\Framework\App\Request\Http $request,
		\Magento\Framework\View\Element\Context $context,
		\Magento\Cms\Model\BlockFactory $blockFactory,
		\Magento\Cms\Model\PageFactory $pageFactory,
		\Magento\Framework\Filesystem\Driver\File $file,
		\Magento\Framework\Xml\Parser $parser,
		\Magento\Cms\Model\Template\FilterProvider $filterProvider,
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Framework\Session\SessionManagerInterface $session
	) {
		$this->session = $session;
		parent::__construct($storeManager, $date, $objectManager, $url, $filesystem, $request, $context, $blockFactory, $pageFactory, $file, $parser, $filterProvider, $customerSession);
	}
	
	// Check to accept to use builder panel
    public function acceptToUsePanel() {
		if($this->_acceptToUsePanel){
			$this->session->setThemeCustomize(false);
			return true;
		}else{
			if ($this->showButton() && ($this->customerSession->getUseFrontendBuilder() == 1)) {
				$this->_acceptToUsePanel = true;
				$this->session->setThemeCustomize(false);
				return true;
			}
			$this->_acceptToUsePanel = false;
			return false;
		}
        
    }

	/* Check to visible panel button */
    public function showButton() {
        if ($this->getStoreConfig('fbuilder/general/is_enabled')) {
            $customer = $this->getCustomer();
			if($customer->getIsFbuilderAccount() == 1){
				return true;
			}
			$this->session->setThemeCustomize(false);
			return false;
        }
		$this->session->setThemeCustomize(false);
        return false;
    }
	
	public function isCustomize(){
		if($this->session->getThemeCustomize()){
			return true;
		}
		return false;
	}
}