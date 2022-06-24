<?php

namespace MGS\Amp\Controller\Cart;

use Magento\Framework\Controller\ResultFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Framework\Exception\NoSuchEntityException;

class Items extends \Magento\Checkout\Controller\Cart\Add {
	/**
     * Initialize product instance from request data
     *
     * @return \Magento\Catalog\Model\Product|false
     */

    public function execute() {
		$sourceOrigin = $this->getRequest()->getParam('__amp_source_origin');

        if (!$sourceOrigin) {
            $urlData  = parse_url($this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB));

            if (!empty($urlData['scheme']) && !empty($urlData['host'])) {
                $_port = !empty($urlData['port']) ? (':' . $urlData['port']) : '';
                $sourceOrigin = $urlData['scheme'] . '://' . $urlData['host'] . $_port;
            }
        }

		$domain_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
		header("Content-type: application/json");
		header("Access-Control-Allow-Origin: *");
		header("AMP-Access-Control-Allow-Source-Origin: ". $sourceOrigin);
		header("Access-Control-Expose-Headers: AMP-Access-Control-Allow-Source-Origin");
		header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS');
		header("Access-Control-Allow-Headers: 'Content-Type, Content-Length, Accept-Encoding, X-CSRF-Token'");
		header("Access-Control-Allow-Credentials: true"); 
		

		$quote = $this->cart->getQuote();
		$items = $quote->getAllVisibleItems();
		$result['items'][0] = [];
		$total = 0;
		$currencyHelper =  \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\Pricing\Helper\Data');
		if(count($items)>0){
			
			$imageHelper =  \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Catalog\Helper\Image');
			$_configAmpHelper = \Magento\Framework\App\ObjectManager::getInstance()->get('MGS\Amp\Helper\Configurable');
			$width = $_configAmpHelper->getStoreConfig('mgs_amp/catalog/minicart_image_width');
			$height = $_configAmpHelper->getStoreConfig('mgs_amp/catalog/minicart_image_height');
			foreach(array_reverse($items) as $item) {
				$total += $item->getQty();
				$result['items'][0]['cart_items'][] = [
					'item_id'=> $item->getId(),
					'name'=> $item->getName(),
					'quantity'=> $item->getQty(),
					'price'=> $currencyHelper->currency(number_format($item->getPrice(),2),true,false),
					'image_url'=> $imageHelper->init($item->getProduct(),'cart_page_product_thumbnail')
                                            ->setImageFile($item->getFile())
                                            ->resize($width,$height)
                                            ->getUrl()
				];      
			} 
		}else{
			$result['items'][0]['is_empty'] = 1;
		}
		$result['items'][0]['total_number'] = $total;
		$result['items'][0]['grand_total'] = $currencyHelper->currency(number_format($quote->getGrandTotal(),2),true,false);

		return $this->getResponse()->setBody(json_encode($result));

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
