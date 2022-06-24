<?php
/**
 * Copyright © 2019 Blueskytechco. All rights reserved. 
 */

namespace Blueskytechco\StoreLocator\Block;

use \Blueskytechco\StoreLocator\Model\ResourceModel\Store\CollectionFactory as StoreCollectionFactory;
use \Magento\Framework\Json\Helper\Data as DataHelper;
use \Blueskytechco\StoreLocator\Helper\Config as ConfigHelper;
use \Blueskytechco\StoreLocator\Model\ResourceModel\Store\Collection as StoreCollection;
use \Blueskytechco\StoreLocator\Model\Store;

class LocationStoresList extends \Magento\Framework\View\Element\Template
{
    
    private $storeCollectionFactory;
    private $dataHelper;
    private $configHelper;
	private $_jsonEncoder;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
		\Magento\Framework\Json\EncoderInterface $jsonEncoder,
        StoreCollectionFactory $storeCollectionFactory,
        DataHelper $dataHelper,
        ConfigHelper $configHelper,
        array $data = []
    ) {
        $this->storeCollectionFactory = $storeCollectionFactory;
        $this->dataHelper = $dataHelper;
		$this->_jsonEncoder = $jsonEncoder;
        $this->configHelper = $configHelper;
        parent::__construct($context, $data);
    }
	
    public function getStoreLocator()
    {
        $locations = $this->storeCollectionFactory->create();
        $locationsArray = [];
        foreach($locations as $location) {
			if($location->getIsActive() == 1){
				$location->load($location->getId());
				$locationsArray[] = $location;
			} 
        }

        return $locationsArray;
    }
	public function getTimeStoreLocator($id)
    {
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$model = $objectManager->create('Blueskytechco\StoreLocator\Model\Store');
        $locations = $model->load($id);
        $time = json_decode($locations->getTimeStore());
		$weekday = date("l");
		$weekday = strtolower($weekday); 
		$weekday_time = $weekday.'_time';
		$weekday_time_today = [];
		$weekday_time_today['today'] = $time->$weekday_time;
		$weekday_time_today['time_today'] = $time->$weekday;
		return $weekday_time_today;
    }
	public function getAllTimeStoreLocator($id)
    {
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$model = $objectManager->create('Blueskytechco\StoreLocator\Model\Store');
		$time_arr = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
        $locations = $model->load($id);
		$time = json_decode($locations->getTimeStore());
		$weekday = date("l");
		$weekday = strtolower($weekday);
		$html = '';
		foreach($time_arr as $arr){
			$weekday_time = $arr.'_time';
			if($weekday == $arr){
				$html .=   '<div class="active"><span>'.$arr.'</span> <span>';
			}else{
				$html .=   '<div><span>'.$arr.'</span> <span>';
			}
			
			if($time->$weekday_time == 0){ 
				$html .= ''.__('Closed').'</span></div>'; 
			}else{ 
				if($time->$arr->from->hours < 10){
					$html .= '0'.$time->$arr->from->hours;
				}else{
					$html .= $time->$arr->from->hours;
				}
				$html .= ' : ';
				if($time->$arr->from->minutes < 10){
					$html .= '0'.$time->$arr->from->minutes;
				}else{
					$html .= $time->$arr->from->minutes;
				} 
				$html .= ' AM - ';
				if($time->$arr->to->hours < 10){
					$html .= '0'.$time->$arr->to->hours;
				}else{
					$html .= $time->$arr->to->hours;
				}
				$html .= ' : ';
				if($time->$arr->to->minutes < 10){
					$html .= '0'.$time->$arr->to->minutes;
				} else{
					$html .= $time->$arr->to->minutes;
				}
				$html .= ' PM </span></div>';
			}
		}
		return $html;
	}
	public function getApiKey()
    {
        $googleApiKey = $this->configHelper->getGoogleApiKeyFrontend(); 
        return $googleApiKey;
    }
	public function getString()
    {
        return '?' . http_build_query($this->getRequest()->getParams());
    }
	public function getJsonLocations()
    {
        $locations = $this->getStoreLocator();
        $locationArray = [];
        $locationArray['items'] = [];
        foreach ($locations as $location) {
            $locationArray['items'][] = $location->getData();
        }
        $locationArray['totalRecords'] = count($locationArray['items']);
        $store = $this->_storeManager->getStore(true)->getId();
        $locationArray['currentStoreId'] = $store;

        return $this->_jsonEncoder->encode($locationArray);
    }
	public function getBaloonTemplate()
    {
		$mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
		$url = $this->_storeManager->getStore()->getUrl('store-locator/store/view/');
        $baloon = 	'<h2><div class="locator-title"><a href="'.$url.'key/{{store_id}}">{{name}}</a></div></h2>  
                    <div class="store">
						<div class="image">
							<img src="'.$mediaUrl.'{{image_store}}" />
						</div>
						<div class="info">
							<p>City: {{city}}</p>
							<p>Zip: {{zip}}</p>
							<p>Country: {{country}}</p>
							<p>Address: {{address}}</p>
						</div>
					</div>	
					<div>
						Description: {{des}} 
					</div>
					';

        $store_url = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $store_url =  $store_url . 'blueskytechco/storeLocator/';

        $baloon = str_replace(
            '{{photo}}',
            '<img src="' . $store_url . '{{photo}}">',
            $baloon
        );

        return $this->_jsonEncoder->encode(array("baloon" => $baloon));
    }
}
