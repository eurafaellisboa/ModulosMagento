<?php
namespace MGS\Amp\Helper;

class Config extends \Magento\Framework\App\Helper\AbstractHelper {
	
	/**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
	protected $_storeManager;
	
	/**
     * @var \Magento\Framework\App\Request\Http
     */
	protected $_request;
	
	/**
     * @var \Magento\Framework\Url
     */
	protected $_urlBuilder;
	
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
     * @param \Magento\Framework\View\Element\Context    	$context
     * @param \Magento\Store\Model\StoreManagerInterface 	$storeManager
     * @param \Magento\Framework\App\Request\Http		 	$request
     * @param \Magento\Framework\Url					 	$urlBuilder
     */
	public function __construct(
		\Magento\Framework\View\Element\Context $context,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\App\Request\Http $request,
		\Magento\Framework\Url $urlBuilder,
		\Magento\Framework\DomDocument\DomDocumentFactory $domFactory
	) {
		$this->scopeConfig = $context->getScopeConfig();
		$this->_storeManager = $storeManager;
		$this->_request = $request;
		$this->_urlBuilder = $urlBuilder;
		$this->domFactory = $domFactory;
	}
	
	/**
     * Check AMP Enable
     * @param  int $storeId
     * @return boolean
     */
    public function enableAmp($storeId = null) {
        return (bool)$this->getStoreConfig('mgs_amp/general/enabled', $storeId);
    }
	
	/**
     * Get Store Config
     * @param  string $node
     * @param  id $storeId NULL
     * @return boolean
     */
	public function getStoreConfig($node, $storeId = NULL){
		if($storeId != NULL){
			return $this->scopeConfig->getValue($node, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
		}
		return $this->scopeConfig->getValue($node, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStore()->getId());
	}
	
	/**
     * Get Current Store
     * @return object
     */
	public function getStore(){
		return $this->_storeManager->getStore();
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
     * Get module enabled and exist request param only-options
     * @return bool
     */
    public function isOnlyOptionsRequest()
    {
        return $this->enableAmp()
            && ($this->_request->getParam('only-options') == 1)
            && ($this->getFullActionName() == 'catalog_product_view');
    }
	
	/**
     * Is AMP the current request
     * @return bool
     */
    public function isAmpCall()
    {
        if ($this->_isAmpPage === null) {
            if (!$this->enableAmp()) {
                return $this->_isAmpPage = false;
            }

            if (!$this->isAllowedPage()) {
                if ($this->getFullActionName() == '__') {
                    return false;
                } else {
                    return $this->_isAmpPage = false;
                }
            }
			/* else{
				if($this->getStoreConfig('mgs_amp/general/amp_mobile')){
					$userAgent = $this->httpHeader->getHttpUserAgent();
					if(\Zend_Http_UserAgent_Mobile::match($userAgent, $_SERVER)){
						return $this->_isAmpPage = true;
					}
				}
			} */

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
	
	

    public function setAmpRequest($value)
    {
        $this->_isAmpPage = (bool)$value;
        return $this;
    }
	
	/**
     * Retrieve allowed full action names
     * @param  int $storeId
     * @return array
     */
    public function getAllowedPages($storeId = null)
    {
        if ($this->_allowedPages === null) {
            $this->_allowedPages = explode(',', $this->getStoreConfig('mgs_amp/general/pages', $storeId));
			
			/* Not Understand */
            $this->_allowedPages[] = 'turpentine_esi_getBlock';
        }

        return $this->_allowedPages;
    }
	
	/**
     * Is current page allowed use AMP
     * @return boolean
     */
    public function isAllowedPage()
    {
        return in_array($this->getFullActionName(), $this->getAllowedPages());
    }
	
	/**
     * @param  array $urlData
     * @param  array $params
     * @return array $urlData
     */
    protected function _mergeUrlParams($urlData, $params)
    {
        if (is_array($params) && count($params)) {
            if (isset($params['_secure'])) {
                $urlData['_secure'] = (bool)$params['_secure'];
                unset($params['_secure']);
            }

            $urlData['query'] = array_merge($urlData['query'], $params);
        }

        return $urlData;
    }
	
	/**
     * @param  string $url
     * @return array $urlData
     */
    protected function _parseUrl($url)
    {
        $url = filter_var($url, FILTER_VALIDATE_URL);
        $url = $url ? $url : $this->_urlBuilder->getCurrentUrl();
        $urlData = parse_url($url);

        if (isset($urlData['query'])) {
            parse_str($urlData['query'], $dataQuery);
            $urlData['query'] = $dataQuery;
        } else {
            $urlData['query'] = [];
        }

        $urlData['fragment'] = isset($urlData['fragment']) ? $urlData['fragment'] : '';

        return $urlData;
    }
	
	/**
     * Retrieve port component from URL data
     * @param  array $urlData
     * @return string
     */
    protected function _getPort($urlData)
    {
        return !empty($urlData['port']) ? (':' . $urlData['port']) : '';
    }
	
	/**
     * String location with amp parameter
     * @return string
     */
    public function getAmpUrl($url = null, $params = null) {
        $urlData = $this->_mergeUrlParams($this->_parseUrl($url), $params);

        if (!isset($urlData['query']['amp'])) {
            $urlData['query'] = array_merge(['amp' => 1], $urlData['query']);
        }

        if (isset($urlData['_secure'])) {
            $urlData['scheme'] = 'https';
        }

        $paramsStr = count($urlData['query'])
            ? '?' . urldecode(http_build_query($urlData['query']))
            : '';

        if (!empty($urlData['fragment'])) {
            $paramsStr .= '#' . $urlData['fragment'];
        }

        return $urlData['scheme'] . '://' . $urlData['host'] . $this->_getPort($urlData) . $urlData['path'] . $paramsStr;
    }
	
	/**
     * Convert HTML To AMP
     * @return string
     */
	public function convertHtmlForAmp($html) {
		$html = '<mgsapmtagfix>' . $html . '</mgsapmtagfix>';
		$domd = $this->domFactory->create();
		libxml_use_internal_errors(true);
		$domd->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
		libxml_use_internal_errors(false);
		$domx = new \DOMXPath($domd);
		
		/* Remove Style Inline */
		$itemsLink = $domx->query("//*[@style]");
		foreach($itemsLink as $item) {
		  $item->removeAttribute("style");
		}
		
		$itemsClick = $domx->query("//*[@onclick]");
		foreach($itemsClick as $item) {
		  $item->removeAttribute("onclick");
		}
		/* Change Image To AMP Image */
		$itemsImg = $domx->query("//img[@src]");
		foreach($itemsImg as $imgItem) {
			list($width, $height, $type, $attr) = getimagesize(str_replace('https://','http://',$imgItem->getAttribute("src")));
			$ampImg = $domd->createElement('amp-img');
			$ampImg->setAttribute('width', $width);
			$ampImg->setAttribute('height', $height);
			$ampImg->setAttribute('layout', 'intrinsic');
			$ampImg->setAttribute('src', $imgItem->getAttribute("src"));
			$imgItem->parentNode->replaceChild($ampImg, $imgItem);
		}
		
		$ampString = $domd->saveHTML();
		$ampString = str_replace('Â', '', $ampString);
		$ampString = str_replace('&#194;', '', $ampString);
		$ampString = str_replace('&Acirc;', '', $ampString);
		unset($domd);
		$ampString = str_replace('<mgsapmtagfix>', '', $ampString);
		$ampString = str_replace('</mgsapmtagfix>', '', $ampString);
		$ampString = str_replace('data-swatches="', 'style="background:', $ampString);
		$ampString = str_replace('data-amp-imageurl', '[src]', $ampString);
		$ampString = str_replace('data-text', '[text]', $ampString);
		$ampString = str_replace('[text]-position', 'data-text-position', $ampString);
		$ampString = str_replace('[text]-animate', 'data-text-animate', $ampString);
		$ampString = str_replace('[text]-align', 'data-text-align', $ampString);
		
		return $ampString;
	}
	
	/**
     * String location without amp parameter
     * @return string
     */
    public function getCanonicalUrl($url = null, $params = null) {
        $urlData = $this->_mergeUrlParams($this->_parseUrl($url), $params);

        if (isset($urlData['query']['amp'])) {
            unset($urlData['query']['amp']);
        }

        if (isset($urlData['_secure'])) {
            $urlData['scheme'] = 'https';
        }

        $paramsStr = count($urlData['query'])
            ? '?' . urldecode(http_build_query($urlData['query']))
            : '';

        if (!empty($urlData['fragment'])) {
            $paramsStr .= '#' . $urlData['fragment'];
        }

        return $urlData['scheme'] . '://' . $urlData['host'] . $this->_getPort($urlData) . $urlData['path'] . $paramsStr;
    }
}