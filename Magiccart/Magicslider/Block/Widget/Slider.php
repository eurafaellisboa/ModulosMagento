<?php
/**
 * Magiccart 
 * @category 	Magiccart 
 * @copyright 	Copyright (c) 2014 Magiccart (http://www.magepow.com/) 
 * @license 	http://www.magepow.com/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2017-01-05 10:40:51
 * @@Modify Date: 2019-09-09 18:09:48
 * @@Function:
 */

namespace Magiccart\Magicslider\Block\Widget;
use Magento\Framework\App\Filesystem\DirectoryList;

class Slider extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{

    const MEDIA_PATH = 'magiccart/magicslider';

    public $_sysCfg;

    public $_urlMedia;

    /**
     * @var \Magento\Framework\Data\CollectionFactory
     */
    protected $_collectionFactory;

    protected $_imageFactory;
    protected $_filesystem;
    protected $_directory;

    protected $_magicslider;
    protected $_images = array();
    protected $_imagesMobile = array();

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Data\CollectionFactory $collectionFactory,
        \Magento\Framework\Image\AdapterFactory $imageFactory,
        \Magiccart\Magicslider\Model\Magicslider $magicslider,
        array $data = []
    ) {

        $this->_collectionFactory = $collectionFactory;
        $this->_imageFactory = $imageFactory;
        $this->_filesystem = $context->getFilesystem();
        $this->_directory = $this->_filesystem->getDirectoryWrite(DirectoryList::MEDIA);

        $this->_magicslider = $magicslider;

        $this->_sysCfg= (object) $context->getScopeConfig()->getValue(
            'magicslider',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        $identifier = $this->getIdentifier();
        // $store = $this->_storeManager->getStore()->getStoreId();
        $item = $this->_magicslider->getCollection()->addFieldToSelect('config')
                        // ->addFieldToFilter('stores',array( array('finset' => 0), array('finset' => $store)))
                        ->addFieldToFilter('status', 1)
                        ->addFieldToFilter('identifier', $identifier)->getFirstItem();
                        
        $data = json_decode($item->getConfig(), true);
        if(!$data){
            echo '<div class="message-error error message">Identifier "'. $identifier . '" not exist.</div> ';          
            return;
        }

        $breakpoints = $this->getResponsiveBreakpoints();
        $total = count($breakpoints);
        $responsive = '[';
        foreach ($breakpoints as $size => $screen) {
            if(isset($data[$screen])){
                $responsive .= '{"breakpoint": '.$size.', "settings": {"slidesToShow": '.$data[$screen].'}}';
            }
            if($total-- > 1) $responsive .= ', ';
        }
        $responsive .= ']';
        $data['responsive'] = $responsive;
        $data['slides-To-Show'] = $data['visible'];
        // $data['swipe-To-Slide'] = 'true';
        $data['vertical-Swiping'] = $data['vertical'];
        $data['slide'] = 1;
        //$data['lazy-Load'] = 'progressive';
        $this->addData($data);
        parent::_construct();
    }

    /**
     * Retrieve media gallery images
     *
     * @return \Magento\Framework\Data\Collection
     */
    public function getMediaGalleryImages()
    {
        $sliderMobile = $this->getMediaGalleryMobileImages();
        $_image = $this->_imageFactory->create();
        $mediaPath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();
        if (!$this->hasData('media_gallery_images') && is_array($this->getMediaGallery('images'))) {
            $images = $this->_collectionFactory->create();
            $i=0;
            foreach ($this->getMediaGallery('images') as $image) {
                if ((isset($image['disabled']) && $image['disabled'])
                ) {
                    continue;
                }
                $image['url'] = $this->getMediaUrl($image['file']);
                if(isset($sliderMobile[$i])){
                    $image['url_mobile'] = $sliderMobile[$i]->getUrl();
                    $file = self::MEDIA_PATH . $sliderMobile[$i]->getFile();
                    $absPath = $mediaPath .$file;
                    $_image->open($absPath);
                    $image['width_mobile'] = $_image->getOriginalWidth();    
                }
                $images->addItem(new \Magento\Framework\DataObject($image));
                $i++;
            }
            $this->setData('media_gallery_images', $images);
        }

        return $this->getData('media_gallery_images');
    }

    /**
     * Retrieve media gallery images
     *
     * @return \Magento\Framework\Data\Collection
     */
    public function getMediaGalleryMobileImages()
    {
        if (!$this->hasData('media_gallery_mobile_images') && is_array($this->getMediaGalleryMobile('images'))) {
            $images = array();
            foreach ($this->getMediaGalleryMobile('images') as $image) {
                if ((isset($image['disabled']) && $image['disabled'])
                ) {
                    continue;
                }
                $image['url'] = $this->getMediaUrl($image['file']);
                $images[] = new \Magento\Framework\DataObject($image);
            }
            $this->setData('media_gallery_mobile_images', $images);
        }

        return $this->getData('media_gallery_mobile_images');
    }

    public function getSlider()
    {
       return $this->getMediaGalleryImages();
    }

    public function getMediaUrl($file)
    {
        if(!$this->_urlMedia) $this->_urlMedia = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

        return $this->_urlMedia .'magiccart/magicslider'. $file;
    }

    public function getImage($file)
    {        
        return $this->getMediaUrl($file);
    }

    public function getVideo($data)
    {
        $url = str_replace('vimeo.com', 'player.vimeo.com/video', $data['video_url']) .'?byline=0&amp;portrait=0&amp;api=1';
        $video = array(
            'url' => $url,
            'width' => '100%',
            'height' => '100%'
        );
        $file = self::MEDIA_PATH. $data['file'];
        $absPath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath().$file;
        $image = $this->_imageFactory->create();
        $image->open($absPath);
        $video['width'] = $image->getOriginalWidth();
        $video['height'] = $image->getOriginalHeight();

        return $video;
    }

    public function getResponsiveBreakpoints()
    {
        return array(1921=>'visible', 1920=>'widescreen', 1479=>'desktop', 1200=>'laptop', 992=>'notebook', 768=>'tablet', 576=>'landscape', 480=>'portrait', 361=>'mobile', 1=>'mobile');
    }

    public function getSlideOptions()
    {
        return array('autoplay', 'arrows', 'autoplay-Speed', 'dots', 'infinite', 'padding', 'vertical', 'vertical-Swiping', 'responsive', 'rows', 'slides-To-Show');
    }

    public function getFrontendCfg()
    { 
        if($this->getSlide()) return $this->getSlideOptions();

        $this->addData(array('responsive' =>json_encode($this->getGridOptions())));
        return array('padding', 'responsive');

    }

    public function getGridOptions()
    {
        $options = array();
        $breakpoints = $this->getResponsiveBreakpoints(); ksort($breakpoints);
        foreach ($breakpoints as $size => $screen) {
            $options[]= array($size-1 => $this->getData($screen));
        }
        return $options;
    }

}
