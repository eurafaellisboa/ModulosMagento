<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MGS\Amp\Controller\Cart;

class Remove extends \Magento\Checkout\Controller\Sidebar\RemoveItem
{
    public function execute()
    {
		$sourceOrigin = $this->getRequest()->getParam('__amp_source_origin');

        if (!$sourceOrigin) {
            $urlData  = parse_url($this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB));

            if (!empty($urlData['scheme']) && !empty($urlData['host'])) {
                $_port = !empty($urlData['port']) ? (':' . $urlData['port']) : '';
                $sourceOrigin = $urlData['scheme'] . '://' . $urlData['host'] . $_port;
            }
        }
		$data = $this->getRequest()->getPostValue();
		if(!empty($data)){
			$domain_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
			header("Content-type: application/json");
			header("Access-Control-Allow-Origin: " . $this->getAccessControlOrigin());
			header("AMP-Access-Control-Allow-Source-Origin: ". $sourceOrigin);
			header("Access-Control-Expose-Headers: AMP-Access-Control-Allow-Source-Origin");
			header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS');
			header("Access-Control-Allow-Headers: 'Content-Type, Content-Length, Accept-Encoding, X-CSRF-Token'");
			header("Access-Control-Allow-Credentials: true"); 
			
			$itemId = $data['item_id'];
			try {
				$this->sidebar->checkQuoteItem($itemId);
				$this->sidebar->removeQuoteItem($itemId);
				return $this->jsonResponse();
			} catch (\Magento\Framework\Exception\LocalizedException $e) {
				return $this->jsonResponse($e->getMessage());
			} catch (\Exception $e) {
				$this->logger->critical($e);
				return $this->jsonResponse($e->getMessage());
			}
			
		}
        
    }
	
	public function getAccessControlOrigin() {
        /**
         * Base way to detecting
         * Detecting source origin by server variable HTTP_ORIGIN
         */
        $request = $this->getRequest();
        if ($request) {
            $httpOrigin = $request->getServer('HTTP_ORIGIN');

            if ($httpOrigin) {
                return $httpOrigin;
            }
        }

        /**
         * Alternative way to detecting
         * Detecting source origin by magento base url
         */
        if ($baseUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB)) {
            $urlData = parse_url($baseUrl);
            if (!empty($urlData['host'])) {
                return ('https://' . str_replace('.', '-', $urlData['host']) . '.' . self::DEFAULT_ACCESS_CONTROL_ORIGIN);
            }
        }

        /**
         * Return source origin by default
         */
        return 'https://' . self::DEFAULT_ACCESS_CONTROL_ORIGIN;
    }
}
