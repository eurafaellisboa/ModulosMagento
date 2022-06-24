<?php
namespace MGS\Amp\Plugin\App;

class ConfigPlugin {
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;

	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $_storeManager;
	
	/**
     * Checking request
     * @var bool
     */
    protected $_isAmpPage;
	
	/**
     * Pages Using AMP
     * @var array
     */
    protected $_allowedPages;
	
    /**
     * @param \MGS\Amp\Helper\Config $configHelper
     * @return  void
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
		\Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_request = $request;
        $this->_storeManager = $storeManager;
    }
	
	public function aroundGetValue(\Magento\Framework\App\Config $subject, callable $proceed, $path) {
		$result = $proceed($path, 'stores', $this->_storeManager->getStore()->getId());
		if($path == 'mgs_ajaxnavigation/general/range_price'){
			$_enableAmp = $proceed('mgs_amp/general/enabled', 'stores', $this->_storeManager->getStore()->getId());
			$allowedPages = $proceed('mgs_amp/general/pages', 'stores', $this->_storeManager->getStore()->getId());
			if($this->isAmpCall($_enableAmp, $allowedPages)){
				return 0;
			}
		}
		
		return $result;
	}
	
	/**
     * Get Current Full Action Name
     * @return string
     */
	public function getFullActionName() {
		if (!$this->_request) {
            return '__';
        }

        return $this->_request->getFullActionName();
	}
	
	/**
     * Is AMP the current request
     * @return bool
     */
    public function isAmpCall($_enableAmp, $allowedPages)
    {
        if ($this->_isAmpPage === null) {
			
			
            if (!$_enableAmp) {
                return $this->_isAmpPage = false;
            }

            if (!$this->isAllowedPage($allowedPages)) {
                if ($this->getFullActionName() == '__') {
                    return false;
                } else {
                    return $this->_isAmpPage = false;
                }
            }

            if ($this->_request->getParam('only-options') == 1) {
                return $this->_isAmpPage = false;
            }

            if ($this->_request->getParam('canonical') == 1) {
                return false;
            }

            if ($this->_request->getParam('amp') == 1) {
                return $this->_isAmpPage = true;
            }

        }
        return $this->_isAmpPage;
    }
	/**
     * Retrieve allowed full action names
     * @param  int $storeId
     * @return array
     */
    public function getAllowedPages($allowedPages) {
        if ($this->_allowedPages === null) {
            $this->_allowedPages = explode(',', $allowedPages);
			
			/* Not Understand */
            $this->_allowedPages[] = 'turpentine_esi_getBlock';
        }

        return $this->_allowedPages;
    }
	
	/**
     * Is current page allowed use AMP
     * @return boolean
     */
    public function isAllowedPage($allowedPages) {
        return in_array($this->getFullActionName(), $this->getAllowedPages($allowedPages));
    }

}
