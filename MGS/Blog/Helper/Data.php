<?php

namespace MGS\Blog\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	protected $_storeManager;
	protected $_date;
	protected $_filter;
	protected $_url;
	protected $_objectManager;

	public function __construct(
		\Magento\Framework\View\Element\Context $context,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\ObjectManagerInterface $objectManager
	) {
		$this->scopeConfig = $context->getScopeConfig();
		$this->_storeManager = $storeManager;
		$this->_objectManager = $objectManager;
		$this->filterManager = $context->getFilterManager();
	}
	public function getStore(){
		return $this->_storeManager->getStore();
	}
	
	/* Get system store config */
	public function getStoreConfig($node, $storeId = NULL){
		if($storeId != NULL){
			return $this->scopeConfig->getValue($node, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
		}
		return $this->scopeConfig->getValue($node, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStore()->getId());
	}
    public function getConfig($key, $store = null)
    {
		return $this->getStoreConfig('blog/' . $key);
    }

    public function getBaseMediaUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
    }

    public function getRoute()
    {
        $route = $this->getConfig('general_settings/route');
        if ($this->getConfig('general_settings/route') == '') {
            $route = 'blog';
        }
        return $this->_storeManager->getStore()->getBaseUrl() . $route;
    }

    public function getTagUrl($tag)
    {
        $route = $this->getConfig('general_settings/route');
        if ($this->getConfig('general_settings/route') == '') {
            $route = 'blog';
        }
        return $this->_storeManager->getStore()->getBaseUrl() . $route . '/tag/' . urlencode($tag);
    }

    public function convertSlashes($tag, $direction = 'back')
    {
        if ($direction == 'forward') {
            $tag = preg_replace("#/#is", "&#47;", $tag);
            $tag = preg_replace("#\\\#is", "&#92;", $tag);
            return $tag;
        }
        $tag = str_replace("&#47;", "/", $tag);
        $tag = str_replace("&#92;", "\\", $tag);
        return $tag;
    }

    public function checkLoggedIn()
    {
        return $this->_objectManager->get('Magento\Customer\Model\Session')->isLoggedIn();
    }
	
    public function getImageThumbnailPost($post)
    {	
		$imageUrl = "";
        $mediaUrl = $this ->_storeManager-> getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
        
		if($post->getVideoThumbId() != "" && $post->getThumbType() == "video"){
            if($post->getThumbnail() == ""){
                return $this->getThumbnailImgVideoPost($post);
            }else {
                $imageUrl = $mediaUrl . $post->getThumbnail();
            }
        }else {
            $imageUrl = $mediaUrl . $post->getThumbnail();
        }
        
        return $imageUrl;
    }
	
	public function getPostUrl($post) {
		$store = $this->_storeManager->getStore()->getCode();
		
		if($store){
			$url = $post->getPostUrlWithNoCategory() . '?___store=' . $store;
		}else{
			$url = $post->getPostUrlWithNoCategory();
		}
		
		return $url;
	}
	
    public function getImagePost($post)
    {	
		$imageUrl = "";
        $mediaUrl = $this ->_storeManager-> getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
        
		if($post->getVideoBigId() != "" && $post->getImageType() == "video"){
            if($post->getImageUrl() == ""){
                return $this->getThumbnailImgVideoPost($post);
            }else {
                $imageUrl = $post->getImageUrl();
            }
        }else {
            $imageUrl = $post->getImageUrl();
        }
        
        return $imageUrl;
    }
    
	public function getVideoThumbUrl($post)
    {	
        if($post->getVideoThumbType() == "youtube"){
            $video_url = 'https://www.youtube.com/watch?v='.$post->getVideoThumbId();
        }else {
            $video_url = 'https://vimeo.com/'.$post->getVideoThumbId();
        }
        
		return $video_url;
    }
	
	public function getVideoThumbUrlArgArray($post)
    {	
        if($post['thumb_type'] == "youtube"){
            $video_url = 'https://www.youtube.com/watch?v='.$post->getVideoThumbId();
        }else {
            $video_url = 'https://vimeo.com/'.$post->getVideoThumbId();
        }
        
		return $video_url;
    }
	
	
	public function getThumbnailImgVideoPost($post)
    {	
		if($post->getThumbType() == "video"){
			if($post->getVideoThumbId() != ""){
				if($post->getVideoThumbType() == "youtube"){
					return 'http://img.youtube.com/vi/'.$post->getVideoThumbId().'/hqdefault.jpg';
				}else {
					$info = 'thumbnail_medium';
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'vimeo.com/api/v2/video/'.$post->getVideoThumbId().'.php');
					curl_setopt($ch, CURLOPT_HEADER, 0);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_TIMEOUT, 10);
					$output = unserialize(curl_exec($ch));
					$output = $output[0][$info];
					curl_close($ch);
					return $output;
				}
			}
			
		}
		return;
    }
	
	public function getGalleryImage($post){
		if($post->getGalleryImage()){
			$result = [];
			$gallery = $post->getGalleryImage();
			$galleryArray = explode(',',$gallery);
			if(count($galleryArray)>0){
				foreach($galleryArray as $img){
					$filePath = 'mgs_blog/gallery/image'.$img;
					if($filePath!=''){
						$imageUrl = $this ->_storeManager-> getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA ) . $filePath;
						$result[] = $imageUrl;
					}
				}
			}
			return $result;
		}
		return 0;
	}
	
	public function getGalleryImageArgArray($post){
		if($post['gallery_image']){
			$result = [];
			$gallery = $post['gallery_image'];
			$galleryArray = explode(',',$gallery);
			if(count($galleryArray)>0){
				foreach($galleryArray as $img){
					$filePath = 'mgs_blog/gallery/image'.$img;
					if($filePath!=''){
						$imageUrl = $this ->_storeManager-> getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA ) . $filePath;
						$result[] = $imageUrl;
					}
				}
			}
			return $result;
		}
		return 0;
	}
	public function truncateString($string, $length){
		return $this->filterManager->truncate($string, ['length' => $length]);
	}
	
	public function truncate($content, $length){
		return $this->filterManager->truncate($content, ['length' => $length, 'etc' => '']);
	}
}
