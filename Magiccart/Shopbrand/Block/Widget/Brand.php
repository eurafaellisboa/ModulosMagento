<?php
/**
 * Magiccart 
 * @category 	Magiccart 
 * @copyright 	Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license 	http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2016-01-05 10:40:51
 * @@Modify Date: 2019-05-21 18:10:09
 * @@Function:
 */

namespace Magiccart\Shopbrand\Block\Widget;
// use Magento\Framework\App\Filesystem\DirectoryList;

class Brand extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{

    public $_sysCfg;

    protected $_imageFactory;
    // protected $_filesystem;
    // protected $_directory;

    protected $_brandCollectionFactory;
    protected $_brands = array();
    protected $_attribute = array();

    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Repository $_productAttributeRepository
     */
    protected $_productAttributeRepository;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Image\AdapterFactory $imageFactory,
        // \Magento\Framework\Filesystem $filesystem,
        \Magento\Catalog\Model\Product\Attribute\Repository $productAttributeRepository,
        \Magiccart\Shopbrand\Model\ResourceModel\Shopbrand\CollectionFactory $brandCollectionFactory,
        array $data = []
    ) {
        $this->_imageFactory = $imageFactory;
        // $this->_filesystem = $filesystem;
        // $this->_directory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);

        $this->_brandCollectionFactory = $brandCollectionFactory;
        $this->_productAttributeRepository = $productAttributeRepository;

        $this->_sysCfg= (object) $context->getScopeConfig()->getValue(
            'shopbrand',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        parent::__construct($context, $data);
    }

    protected function _construct()
    {

		$data = $this->_sysCfg->general;
		//$dataConvert = array('infinite', 'vertical', 'autoplay', 'centerMode');
		if($data['slide']){
			$data['vertical-Swiping'] = $data['vertical'];
            $breakpoints = $this->getResponsiveBreakpoints();
            $responsive = '[';
            $num = count($breakpoints);
            foreach ($breakpoints as $size => $opt) {
            	$item = (int) $data[$opt];
            	$responsive .= '{"breakpoint": "'.$size.'", "settings": {"slidesToShow": "'.$item.'"}}';
                $num--;
            	if($num) $responsive .= ', ';
            }
            $responsive .= ']';
            $data['slides-To-Show'] = $data['visible'];
            // $data['swipe-To-Slide'] = 'true';
			$data['responsive'] = $responsive;
		}

        // $data['selector'] = 'alo-slider'.md5(rand());
        $this->addData($data);

        parent::_construct();

    }

    public function getBrands()
    {
        if(!$this->_brands){
            $store = $this->_storeManager->getStore()->getStoreId();
            $brands = $this->_brandCollectionFactory->create()
                        ->addFieldToFilter('stores',array( array('finset' => 0), array('finset' => $store)))
                        ->addFieldToFilter('status', 1);

            $this->_brands = $brands->setOrderByBrand();
        }
        return $this->_brands;
    }

    public function getUrlBrand($brand)
    {
        $typeLink = $this->getData('link');
        $baseUrl  = $this->_storeManager->getStore()->getBaseUrl();
        $attrCode = $this->getData('attributeCode');
        $link = '#';
        if(!$typeLink) $link = $brand->getUrlkey() ? $baseUrl . $brand->getUrlkey() : '#';
        elseif($typeLink == '2' && $brand->getOptionId()){
            $link = $baseUrl . 'catalogsearch/advanced/result/?' . $attrCode . urlencode('[]') . '=' . $brand->getOptionId();
        } elseif($typeLink == '1') {
            $attr = $this->getAttribute();
            if($attr->usesSource()){
                $option  = $attr->getSource()->getOptionText($brand->getOptionId());
                $link = $baseUrl . 'catalogsearch/result/?q=' .$option; 
            }
        }
        return $link;
    }

    public function getAttribute()
    {
        if (!$this->_attribute) {
            $attr = $this->getData('attributeCode');
            $this->_attribute = $this->_productAttributeRepository->get($attr); // ->getOptions();
        }
        return $this->_attribute;         
    }

    public function getImage($object)
    {
        // $width  =200;
        // $height = 200;
        // $directory = $width . 'x' . $height;
        // $image = $object->getImage();
        // $absPath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath().$image;
        // $imageResized = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath($directory).$image;
        // $imageResize = $this->_imageFactory->create();
        // $imageResize->open($absPath);
        // $imageResize->constrainOnly(TRUE);
        // $imageResize->keepTransparency(TRUE);
        // $imageResize->keepFrame(FALSE);
        // $imageResize->keepAspectRatio(true);
        // $imageResize->resize($width, $height);
        // $dest = $imageResized ;
        // $imageResize->save($dest);
        // $resizedURL= $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).$directory.$image;
        
        $resizedURL = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $object->getImage();
        return $resizedURL;
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
