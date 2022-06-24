<?php

declare(strict_types=1);

namespace Blueskytechco\SetProduct\Block\Widget;

use Magento\Widget\Block\BlockInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\App\Filesystem\DirectoryList;

class SetProduct extends Template  implements BlockInterface
{
    protected $_lookCollectionFactory;

    protected $_templateFilterContent;

    protected $_geDir;

    protected $_getFile;

    protected $_parser;

    public function __construct(
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Framework\View\Element\Template\Context $context,
        \Blueskytechco\SetProduct\Model\ResourceModel\ProductSet\CollectionFactory  $lookCollectionFactory,
        \Magento\Framework\Filesystem $file,
        array $data = []
    ) {
        $this->_templateFilterContent = $filterProvider;
        $this->_lookCollectionFactory = $lookCollectionFactory;
        $this->_getFile = $file;
        $this->_parser = new \Magento\Framework\Xml\Parser();
        $this->_geDir = $this->_getFile->getDirectoryRead(DirectoryList::APP)->getAbsolutePath('code/Blueskytechco/PageBuilderCustom/size_thumbnail');
        parent::__construct($context, $data);
    }

    public function getCol()
    {
        $col_xxl = $this->getDataWidgetConfig('col_xxl');
        $col_xl = $this->getDataWidgetConfig('col_xl');
        $col_lg = $this->getDataWidgetConfig('col_lg');
        $col_md = $this->getDataWidgetConfig('col_md');
        $col_sm = $this->getDataWidgetConfig('col_sm');
        $col_xs = $this->getDataWidgetConfig('col_xs');
        return ' col-xxl-'.$col_xxl.' col-xl-'.$col_xl.' col-lg-'.$col_lg.' col-md-'.$col_md.' col-sm-'.$col_sm.' col-'.$col_xs;
    }

    public function getLookbookItems()
    {
    	$lookbook_id = explode(',', $this->getDataWidgetConfig('lookbook_id'));
        $collection = $this->_lookCollectionFactory->create();
        $collection->addFieldToFilter('identifier', ['in'=>$lookbook_id]);
        $collection->getSelect()->order("find_in_set(identifier,'".implode(',',$lookbook_id)."')");
        return $collection;
    }

    public function getTitleLinkHtml($lookbook)
    {
    	$html = '';
    	if($lookbook->getTitle() != ''){
        	$html = '<a href="'.$lookbook->getTitleLink().'">'.$lookbook->getTitle().'</a>';
    	}
        return $html;
    }

    public function getPin($lookbook)
    {
        $html = '';
        
        if($lookbook->getProductData() && $lookbook->getProductData() != ''){
            $pro_decode = json_decode($lookbook->getProductData(), true);
            $a_fix = '<a href="javascript:void(0);" rel="nofollow" class="pin-icon-product">';
            $style = '';
            if($this->getDataWidgetConfig('bg_color') != '' || $this->getDataWidgetConfig('text_color') != ''){
                $arr_style = [];
                if($this->getDataWidgetConfig('bg_color') != ''){
                    $arr_style[] = 'background-color: '.$this->getDataWidgetConfig('bg_color');
                }
                if($this->getDataWidgetConfig('text_color') != ''){
                    $arr_style[] = 'color: '.$this->getDataWidgetConfig('text_color');
                }
                $style = ' style="'.implode("; ", $arr_style).'"';
            }
            foreach ($pro_decode as $key_pro => $val_pro) {
                if($this->getDataWidgetConfig('open_in') == 'quickview' && isset($val_pro['product_id']) && $val_pro['product_id'] != ''){
                    $a_fix = '<a href="javascript:void(0);" data-product-id="'.$val_pro['product_id'].'" data-quickview-url="'.$this->getUrl('blueskytechco_quickview/product/view').'id/'.$val_pro['product_id'].'" rel="nofollow" class="pin-icon-product action link-quickview">';
                }
                else{
                    if(isset($val_pro['product_id']) && $val_pro['product_id'] != ''){
                        $a_fix = '<a href="javascript:void(0);" data-product-id="'.$val_pro['product_id'].'" data-shortview-url="'.$this->getUrl('blueskytechco_quickview/product/shortview').'id/'.$val_pro['product_id'].'" rel="nofollow" class="pin-icon-product link-shortview">';
                    }
                }
                $html .= '<div data-pin="'.$key_pro.'" class="lookbook-icon '.$lookbook->getButtonStyle().'" id="pin_'.$key_pro.'" style="top: '.$val_pro['top'].'%; left: '.$val_pro['left'].'%;">'.$a_fix.'<span class="pin-content-product"'.$style.'>'.$val_pro['label'].'</span></a></div>';
            }
        }
        
        return $html;
    }

    public function getBannerImageSrc($lookbook)
    {
        $t = '//placehold.jp/1aada3/fff/1000x1000.png?text=Image';
        $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        if($image = $lookbook->getBannerImage()){
            if (strpos($image, 'placehold.jp') === false) {
                $t = $mediaUrl . $image;
            }
            else{
                $t = $image;
            }
        }
        return $t;
    }

    public function getDataWidgetConfig($path)
    {
        return $this->getData($path) ?: '';
    }

    public function filterOutputContent($content)
    {
        $content = (string) $content ?: '';
        if($content != ''){
            $arr_encode = ['^[','^]','`','|','&lt;','&gt;'];
            $arr_decode = ['{','}','"','\\','<','>'];
            $new_content = str_replace($arr_encode, $arr_decode, $content);
            return $this->_templateFilterContent->getPageFilter()->filter(
                (string) $new_content ?: ''
            );
        }
        return '';
    }
}