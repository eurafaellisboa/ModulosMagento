<?php

declare(strict_types=1);

namespace Blueskytechco\Instagram\Block\Widget;

use Magento\Widget\Block\BlockInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\App\Filesystem\DirectoryList;

class Instagram extends Template  implements BlockInterface
{
    protected $_instagrampostFactory;

    protected $_templateFilterContent;

    protected $_geDir;

    protected $_getFile;

    protected $_parser;

    public function __construct(
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Framework\View\Element\Template\Context $context,
        \Blueskytechco\Instagram\Model\InstagrampostFactory $instagrampostFactory,
        \Magento\Framework\Filesystem $file,
        array $data = []
    ) {
        $this->_templateFilterContent = $filterProvider;
        $this->_instagrampostFactory =  $instagrampostFactory;
        $this->_getFile = $file;
        $this->_parser = new \Magento\Framework\Xml\Parser();
        $this->_geDir = $this->_getFile->getDirectoryRead(DirectoryList::APP)->getAbsolutePath('code/Blueskytechco/PageBuilderCustom/size_thumbnail');
        parent::__construct($context, $data);
    }


    public function _toHtml()
    {
        if($this->getDataWidgetConfig('type_name') == 'carousel'){
            $this->setTemplate(
                'Blueskytechco_Instagram::widget/carousel.phtml'
            );
        }
        else{
            $this->setTemplate(
                'Blueskytechco_Instagram::widget/default.phtml'
            );
        }

        $html = parent::_toHtml();
        return $html;
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

    public function getInstagramPostByStoreView()
    {
        $storeId = $this->_storeManager->getStore()->getId();
        $post_in = $this->_instagrampostFactory->create();
        $collection = $post_in->getCollection()->addFieldToFilter('store',['eq' => $storeId]);
        if($collection->count() <= 0){
            $collection_default = $post_in->getCollection()->addFieldToFilter('store',['eq' => 0]);
            return $collection_default;
        }
        return $collection;
    }

    public function getSizeThumbnail()
    {
        if (is_readable($this->_geDir.'/instagram.xml'))
        {
            $data_sizes = $this->_parser->load($this->_geDir.'/instagram.xml')->xmlToArray();
            $common = [];
            $type_view = $this->getDataWidgetConfig('type_name');
            if(isset($data_sizes['root'][$type_view]['common'])){
                $common = $data_sizes['root'][$type_view]['common'];
            }
            
            return ['common' => $common];
        }
        return [];
    }

    public function getColBootstrap()
    {
        $col_xxl = $this->getDataWidgetConfig('col_xxl');
        $col_xl = $this->getDataWidgetConfig('col_xl');
        $col_lg = $this->getDataWidgetConfig('col_lg');
        $col_md = $this->getDataWidgetConfig('col_md');
        $col_sm = $this->getDataWidgetConfig('col_sm');
        $col_xs = $this->getDataWidgetConfig('col_xs');
        return ' col-xxl-'.$col_xxl.' col-xl-'.$col_xl.' col-lg-'.$col_lg.' col-md-'.$col_md.' col-sm-'.$col_sm.' col-'.$col_xs;
    }
}