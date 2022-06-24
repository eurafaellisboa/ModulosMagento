<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MGS\Amp\Controller\Newsletter;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Newsletter\Model\Subscriber;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class NewAction extends \Magento\Newsletter\Controller\Subscriber\NewAction
{
    /**
     * New subscription action
     *
     * @return void
     */
    public function execute()
    {
        if ($this->getRequest()->isPost() && $this->getRequest()->getPost('email')) {
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
			
            $email = (string)$this->getRequest()->getPost('email');

            try {
                $this->validateEmailFormat($email);
                $this->validateGuestSubscription();
                $this->validateEmailAvailable($email);

                $subscriber = $this->_subscriberFactory->create()->loadByEmail($email);
                if ($subscriber->getId()
                    && (int) $subscriber->getSubscriberStatus() === Subscriber::STATUS_SUBSCRIBED
                ) {
                    return $this->getResponse()->setBody(json_encode(array('error'=>__('This email address is already subscribed.'))));
                }

                $status = (int) $this->_subscriberFactory->create()->subscribe($email);
				
				if ($status === Subscriber::STATUS_NOT_ACTIVE) {
					return $this->getResponse()->setBody(json_encode(array('success'=>__('The confirmation request has been sent.'))));
				}

				return $this->getResponse()->setBody(json_encode(array('success'=>__('Thank you for your subscription.'))));
            } catch (LocalizedException $e) {
				return $this->getResponse()->setBody(json_encode(array('error'=>__('There was a problem with the subscription: %1', $e->getMessage()))));
            } catch (\Exception $e) {
				return $this->getResponse()->setBody(json_encode(array('error'=>__('Something went wrong with the subscription.'))));
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
