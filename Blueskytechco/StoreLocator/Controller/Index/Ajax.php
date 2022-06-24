<?php


namespace Blueskytechco\StoreLocator\Controller\Index;

use \Blueskytechco\StoreLocator\Model\ResourceModel\Store\CollectionFactory as StoreCollectionFactory;
use \Magento\Framework\Json\Helper\Data as DataHelper;
use \Blueskytechco\StoreLocator\Helper\Config as ConfigHelper;
use \Magento\Store\Model\StoreManagerInterface;
use \Blueskytechco\StoreLocator\Model\ResourceModel\Store\Collection as StoreCollection;
use \Blueskytechco\StoreLocator\Model\Store;

class Ajax  extends \Magento\Framework\App\Action\Action
{

    protected $_coreRegistry;
    protected $_objectManager;
    protected $_scopeConfig;
    protected $_filesystem;
	protected $storeCollectionFactory;
	protected $request;
	protected $_storeManager;
    protected $_jsonEncoder;
	protected $_assetRepo;


    public function __construct(
        \Magento\Framework\App\Action\Context $context,
		\Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
		\Magento\Framework\View\Asset\Repository $assetRepo,
		StoreManagerInterface $storeManager,
		StoreCollectionFactory $storeCollectionFactory
    ) {
		$this->storeCollectionFactory = $storeCollectionFactory;
        $this->_objectManager = $context->getObjectManager();
		$this->_storeManager = $storeManager;
		$this->request = $request;
        $this->_jsonEncoder = $jsonEncoder;
		$this->_assetRepo = $assetRepo;
        parent::__construct($context);
    }


    public function execute()
    {
		$post = $this->request->getPost();
        $locations = $this->storeCollectionFactory->create();
		$mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $this->_view->loadLayout();
        $html = ''; 
		$html .= '<div id="store_list">';	
        $arrayCollection = [];
		$locationsArray = [];
		$i = 1;
        
		if(!$post->value){
			foreach ($locations as $item) {
				$arrayCollection['items'][] = $item->getData();
				$item->load($item->getId());
				$locationsArray[] = $item;
				
				$html .= '<div class="list" name="mapLocation" data-id="'.$i.'">';
				if($item->getImageStore()){ 
					$image = $mediaUrl.json_decode($item->getImageStore());
				}else{
					$image = $this->_assetRepo->getUrl("Blueskytechco_StoreLocator::images/shop.jpg");
				}
				$html .= '<div class="image"><img src="'.$image.'"/></div>';
				$html .= '<div class="location-information"><h2>'.$item->getName().'</h2>';
				if (trim($item->getCity())) {
					$html .= '<div>'.__('City').': '.$item->getCity().'</div>';
				}
				if (trim($item->getPostcode())) {
					$html .= '<div>'.__('Zip').': '.$item->getPostcode().'</div>';
				}
				if (trim($item->getCountry())) {
					$html .= '<div>'.__('Country').': '.$item->getCountry().'</div>';
				}
				if (trim($item->getAddress())) {
					$html .= '<div>'.__('Address').': '.$item->getAddress().'</div>';
				}
				$html .= '<div class="view-detail"><a href="'.$this->_storeManager->getStore()->getUrl('store-locator/store/view/key/'.$item->getId()).'">'.__('View Detail').'</a></div>';
				$html .= '</div>';
				$time_today = $this->getTimeStoreLocator($item->getStoreId());
				$html .= '<div class="today_time">'.__('Opening Hours:').''; 
				if($time_today['today'] == 0){
					$html .= ''.__('Closed').'';
				}else{ 
					if($time_today['time_today']->from->hours < 10){
						$html .= '0'.$time_today['time_today']->from->hours; 
					} 
					$html .= ' : '; 
					if($time_today['time_today']->from->minutes < 10){
						$html .= '0'.$time_today['time_today']->from->minutes;
					} 
					$html .= ' AM - '; 
					if($time_today['time_today']->to->hours < 10){
						$html .= '0'.$time_today['time_today']->to->hours;
					} 
					$html .= ' : ';
					if($time_today['time_today']->to->minutes < 10){
						$html .= '0'.$time_today['time_today']->to->minutes;
					} 
					$html .= ' PM '; 
				}
				$html .= '<div class="locator_arrow"></div></div><div class="all_today_time">';
				
				$html .= $this->getAllTimeStoreLocator($item->getStoreId());
				$html .= '</div></div>';
				$i++;
			}
		}else{
			foreach ($locations as $item) {
				if($item->getIsActive() == 1){
					$lat = $item->getLat();
					$lng = $item->getLng();
					$arr_lat = explode('.',$lat);
					$arr_lat_1 = (int)$arr_lat[0];
					
					$arr_lng = explode('.',$lng);
					$arr_lng_1 = (int)$arr_lng[0];
					
					$post_arr_lat = explode('.',$post->lat);
					$post_arr_lat_1 = (int)$post_arr_lat[0];
					
					$post_arr_lng = explode('.',$post->lng);
					$post_arr_lng_1 = (int)$post_arr_lng[0];
					
					$post_value = $post->value;
					$contry = $item->getCountryName();
					$address = $item->getAddress();
					$city = $item->getCity();
					if($arr_lat_1 == $post_arr_lat_1 && $arr_lng_1 == $post_arr_lng_1){
						$arrayCollection['items'][] = $item->getData();
						$item->load($item->getId());
						$locationsArray[] = $item;
						
						$html .= '<div class="list" name="mapLocation" data-id="'.$i.'">';
						if($item->getImageStore()){ 
							$image = $mediaUrl.json_decode($item->getImageStore());
						}else{
							$image = $this->_assetRepo->getUrl("Blueskytechco_StoreLocator::images/shop.jpg");
						}
						$html .= '<div class="image"><img src="'.$image.'"/></div>';
						$html .= '<div class="location-information"><h2>'.$item->getName().'</h2>';
						if (trim($item->getCity())) {
							$html .= '<div>'.__('City').': '.$item->getCity().'</div>';
						}
						if (trim($item->getPostcode())) {
							$html .= '<div>'.__('Zip').': '.$item->getPostcode().'</div>';
						}
						if (trim($item->getCountry())) {
							$html .= '<div>'.__('Country').': '.$item->getCountry().'</div>';
						}
						if (trim($item->getAddress())) {
							$html .= '<div>'.__('Address').': '.$item->getAddress().'</div>';
						}
						$html .= '<div class="view-detail"><a href="'.$this->_storeManager->getStore()->getUrl('store-locator/store/view/key/'.$item->getId()).'">'.__('View Detail').'</a></div>';
						$html .= '</div>';
						$time_today = $this->getTimeStoreLocator($item->getStoreId());
						$html .= '<div class="today_time">'.__('Opening Hours:').''; 
						if($time_today['today'] == 0){
							$html .= ''.__('Closed').'';
						}else{ 
							if($time_today['time_today']->from->hours < 10){
								$html .= '0'.$time_today['time_today']->from->hours;
							} 
							$html .= ' : '; 
							if($time_today['time_today']->from->minutes < 10){
								$html .= '0'.$time_today['time_today']->from->minutes;
							} 
							$html .= ' AM - '; 
							if($time_today['time_today']->to->hours < 10){
								$html .= '0'.$time_today['time_today']->to->hours;
							} 
							$html .= ' : ';
							if($time_today['time_today']->to->minutes < 10){
								$html .= '0'.$time_today['time_today']->to->minutes;
							} 
							$html .= ' PM '; 
						}
						$html .= '<div class="locator_arrow"></div></div><div class="all_today_time">';
						
						$html .= $this->getAllTimeStoreLocator($item->getStoreId());
						$html .= '</div></div>';
						$i++;
					}elseif(strpos($post_value, $contry) !== false || strpos($post_value, $address) !== false || strpos($post_value, $city) !== false){
						$arrayCollection['items'][] = $item->getData();
						$item->load($item->getId());
						$locationsArray[] = $item;
						
						$html .= '<div class="list" name="mapLocation" data-id="'.$i.'">';
						if($item->getImageStore()){ 
							$image = $mediaUrl.json_decode($item->getImageStore());
						}else{
							$image = $this->_assetRepo->getUrl("Blueskytechco_StoreLocator::images/shop.jpg");
						}
						$html .= '<div class="image"><img src="'.$image.'"/></div>';
						$html .= '<div class="location-information"><h2>'.$item->getName().'</h2>';
						if (trim($item->getCity())) {
							$html .= '<div>'.__('City').': '.$item->getCity().'</div>';
						}
						if (trim($item->getPostcode())) {
							$html .= '<div>'.__('Zip').': '.$item->getPostcode().'</div>';
						}
						if (trim($item->getCountry())) {
							$html .= '<div>'.__('Country').': '.$item->getCountry().'</div>';
						}
						if (trim($item->getAddress())) {
							$html .= '<div>'.__('Address').': '.$item->getAddress().'</div>'; 
						}
						$html .= '<div class="view-detail"><a href="'.$this->_storeManager->getStore()->getUrl('store-locator/store/view/key/'.$item->getId()).'">'.__('View Detail').'</a></div>';
						$html .= '</div>';
						$time_today = $this->getTimeStoreLocator($item->getStoreId());
						$html .= '<div class="today_time">'.__('Opening Hours:').''; 
						if($time_today['today'] == 0){
							$html .= ''.__('Closed').'';
						}else{ 
							if($time_today['time_today']->from->hours < 10){
								$html .= '0'.$time_today['time_today']->from->hours;
							} 
							$html .= ' : '; 
							if($time_today['time_today']->from->minutes < 10){
								$html .= '0'.$time_today['time_today']->from->minutes;
							} 
							$html .= ' AM - '; 
							if($time_today['time_today']->to->hours < 10){
								$html .= '0'.$time_today['time_today']->to->hours;
							} 
							$html .= ' : ';
							if($time_today['time_today']->to->minutes < 10){
								$html .= '0'.$time_today['time_today']->to->minutes;
							} 
							$html .= ' PM '; 
						}
						$html .= '<div class="locator_arrow"></div></div><div class="all_today_time">';
						
						$html .= $this->getAllTimeStoreLocator($item->getStoreId());
						$html .= '</div></div>';
						$i++;
					}
				}
			}
        }
		$html .= '</div>';	
		$locations = $locationsArray;
        $locationArray = [];
        $locationArray['items'] = [];
        foreach ($locations as $location) {
            $locationArray['items'][] = $location->getData();
        }
        $locationArray['totalRecords'] = count($locationArray['items']);
        $store = $this->_storeManager->getStore(true)->getId();
        $locationArray['currentStoreId'] = $store;

        $locationArray =  $locationArray;
		$arrayCollection['locator']['lat'] = $post->lat;
		$arrayCollection['locator']['lng'] = $post->lng;  
        $arrayCollection['totalRecords'] = isset($arrayCollection['items']) ? count($arrayCollection['items']) : 0;

        $res = array_merge_recursive(
            $arrayCollection, array('block' => $html),array('locations' => $locationArray)
        );

        $json = $this->_jsonEncoder->encode($res);

        $this->getResponse()->setBody($json);
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
					$html .= $time->$arr->from->hours;
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
}
