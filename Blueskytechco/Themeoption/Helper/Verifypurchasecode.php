<?php

namespace Blueskytechco\Themeoption\Helper;

class Verifypurchasecode extends \Magento\Framework\App\Helper\AbstractHelper
{
	protected $_configFactory;

	protected $_remoteAddress;

	public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        \Magento\Framework\App\Config\ConfigResource\ConfigInterface $configFactory
    ) {
       	$this->_configFactory = $configFactory;
       	$this->_remoteAddress = $remoteAddress;
        parent::__construct($context);
    }

    public function checkEnvatoPurchaseCode() {
    	if($this->checkLocalHost()){
            return true;
        }
        
    	$purchasecode = $this->scopeConfig->getValue('activationcode/activation/purchasecode');
        $purchasecode_confirm = $this->scopeConfig->getValue('activationcode/activation/purchasecode_confirm');

        if($purchasecode != '' && base64_encode($purchasecode) == $purchasecode_confirm){
        	return true;
        }
    	return false;
    }

    public function verifyEnvatoPurchaseCode() {
    	if($this->checkLocalHost()){
            return 5;
        }

        $purchasecode = $this->scopeConfig->getValue('activationcode/activation/purchasecode');
        $purchasecode_confirm = $this->scopeConfig->getValue('activationcode/activation/purchasecode_confirm');

        $base_url = $this->scopeConfig->getValue('web/unsecure/base_url');
        $get_domain = trim(preg_replace('/^.*?\\/\\/(.*)?\\//', '$1', $base_url));
        if(strpos($get_domain, "/")){
			$get_domain = substr($get_domain, 0, strpos($get_domain, "/"));
        }

        if($purchasecode_confirm != ''){
            if(!$purchasecode || $purchasecode == '' || base64_encode($purchasecode) != $purchasecode_confirm) {
                $this->checkPurchaseCode(base64_decode($purchasecode_confirm), $get_domain, "r");
            }
        }

        if($purchasecode && $purchasecode != ''){
        	$re = $this->checkPurchaseCode($purchasecode, $get_domain, "a");
        	if(isset($re['result']) && $re['result'] == 'success'){
        		$this->_configFactory->saveConfig('activationcode/activation/purchasecode_confirm',base64_encode($purchasecode),"default",0);
        	}
        	
        	return $re;
        }
    	return 1;
    }

    public function checkPurchaseCode($purchasecode, $get_domain, $action) {
        $header   = [];
      	$header[] = 'Content-length: 0';
      	$header[] = 'Content-type: application/json; charset=utf-8';

      	$verify_url = 'https://blueskytechmage.com/api/verify_purchase.php';
      	$ch_verify = curl_init( $verify_url . '?item=36891241&purchasecode=' . $purchasecode.'&get_domain='.$get_domain.'&action='.$action);

      	curl_setopt( $ch_verify, CURLOPT_HTTPHEADER, $header );
		curl_setopt( $ch_verify, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch_verify, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch_verify, CURLOPT_CONNECTTIMEOUT, 5 );
		curl_setopt( $ch_verify, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');

		$cinit_verify_data = curl_exec( $ch_verify );
		curl_close( $ch_verify );

        $result = json_decode($cinit_verify_data, true);
        return $result;
    }


    public function checkLocalHost() {
        $locallist = ['127.0.0.1', '::1', 'locallhost'];
        $ip = $this->_remoteAddress->getRemoteAddress();
        
        if($ip && in_array($ip, $locallist)){
        	return true;
        }
        return false;
    }
}