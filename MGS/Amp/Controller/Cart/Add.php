<?php

namespace MGS\Amp\Controller\Cart;

use Magento\Framework\Controller\ResultFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Framework\Exception\NoSuchEntityException;

class Add extends \Magento\Checkout\Controller\Cart\Add {
	/**
     * Initialize product instance from request data
     *
     * @return \Magento\Catalog\Model\Product|false
     */
    protected function _initProduct()
    {
        $productId = (int)$this->getRequest()->getParam('product');
        if ($productId) {
            $storeId = $this->_objectManager->get(
                \Magento\Store\Model\StoreManagerInterface::class
            )->getStore()->getId();
            try {
                return $this->productRepository->getById($productId, false, $storeId);
            } catch (NoSuchEntityException $e) {
                return false;
            }
        }
        return false;
    }
	
    public function execute() {
		$sourceOrigin = $this->getRequest()->getParam('__amp_source_origin');

        if (!$sourceOrigin) {
            $urlData  = parse_url($this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB));

            if (!empty($urlData['scheme']) && !empty($urlData['host'])) {
                $_port = !empty($urlData['port']) ? (':' . $urlData['port']) : '';
                $sourceOrigin = $urlData['scheme'] . '://' . $urlData['host'] . $_port;
            }
        }
		if(!empty($_POST)){
			$domain_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
			header("Content-type: application/json");
			header("Access-Control-Allow-Origin: " . $this->getAccessControlOrigin());
			header("AMP-Access-Control-Allow-Source-Origin: ". $sourceOrigin);
			header("Access-Control-Expose-Headers: AMP-Access-Control-Allow-Source-Origin");
			header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS');
			header("Access-Control-Allow-Headers: 'Content-Type, Content-Length, Accept-Encoding, X-CSRF-Token'");
			header("Access-Control-Allow-Credentials: true"); 
			
			$params = $this->getRequest()->getParams();
			try {
				if (isset($params['qty'])) {
					$filter = new \Zend_Filter_LocalizedToNormalized(
						['locale' => $this->_objectManager->get('Magento\Framework\Locale\ResolverInterface')->getLocale()]
					);
					$params['qty'] = $filter->filter($params['qty']);
				}

				$product = $this->_initProduct();
				/**
				 * Check product availability
				 */
				
				if (!$product) {
					$message = __('Something when wrong. We can\'t the product.');
					return $this->getResponse()->setBody(json_encode(array('error'=>$message)));
				}

				$this->cart->addProduct($product, $params);

				$this->cart->save();
				/**
				 * @todo remove wishlist observer \Magento\Wishlist\Observer\AddToCart
				 */
				$this->_eventManager->dispatch(
					'checkout_cart_add_product_complete',
					['product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse()]
				);

				if (!$this->_checkoutSession->getNoCartRedirect(true)) {
					if (!$this->cart->getQuote()->getHasError()) {
						$message = __(
							'You added %1 to your shopping cart.',
							$product->getName()
						);
						return $this->getResponse()->setBody(json_encode(array('success'=>$message)));
					}
				}
			} catch (\Magento\Framework\Exception\LocalizedException $e) {
				if ($this->_checkoutSession->getUseNotice(true)) {
					return $this->getResponse()->setBody(json_encode(array('error'=>$e->getMessage())));
				} else {
					$messages = array_unique(explode("\n", $e->getMessage()));
					return $this->getResponse()->setBody(json_encode(array('error'=>$messages[0])));
				}

				return;
			} catch (\Exception $e) {
				return $this->getResponse()->setBody(json_encode(array('error'=>__('Something when wrong. We can\'t the product.'))));
			}
		}
		
		return;
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
