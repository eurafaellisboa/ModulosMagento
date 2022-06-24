<?php

declare(strict_types=1);

namespace Blueskytechco\WidgetCategory\Block\Widget;

use Magento\Widget\Block\BlockInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\App\Filesystem\DirectoryList;

class CategoryThumbnail extends Template  implements BlockInterface
{
    protected $_templateFilterContent;

    protected $_catCollectionFactory;

    protected $_imageUploader;

    protected $_geDir;

    protected $_getFile;

    protected $_parser;

    public function __construct(
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $collectionFactory,
        \Blueskytechco\WidgetCategory\Model\ImageUploader $imageUploader,
        \Magento\Framework\Filesystem $file,
        array $data = []
    ) {
        $this->_catCollectionFactory = $collectionFactory;
        $this->_templateFilterContent = $filterProvider;
        $this->_imageUploader = $imageUploader;
        $this->_getFile = $file;
        $this->_parser = new \Magento\Framework\Xml\Parser();
        $this->_geDir = $this->_getFile->getDirectoryRead(DirectoryList::APP)->getAbsolutePath('code/Blueskytechco/PageBuilderCustom/size_thumbnail');
        parent::__construct($context, $data);
    }


    public function _toHtml()
    {
        if($this->getDataWidgetConfig('type_name') == 'carousel'){
            $this->setTemplate(
                $this->getDataWidgetConfig('template_carousel_id') ? 'Blueskytechco_WidgetCategory::widget/category_thumbnail/carousel/'.$this->getDataWidgetConfig('template_carousel_id').'.phtml' : 'Blueskytechco_WidgetCategory::widget/category_thumbnail/carousel/carousel.phtml'
            );
        }
        else{
            $this->setTemplate(
                $this->getDataWidgetConfig('template_id') ? 'Blueskytechco_WidgetCategory::widget/category_thumbnail/grid/'.$this->getDataWidgetConfig('template_id').'.phtml' : 'Blueskytechco_WidgetCategory::widget/category_thumbnail/grid/default.phtml'
            );
        }

        $html = parent::_toHtml();
        return $html;
    }

    public function getCatCollection()
    {
        $category_ids = explode(',', $this->getDataWidgetConfig('category_ids'));
        $collection = $this->_catCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addAttributeToFilter('entity_id', ['in'=>$category_ids]);
        $collection->getSelect()->order("find_in_set(entity_id,'".implode(',',$category_ids)."')");
        
        return $collection;
    }

    public function getCategoryThumnnail($widget_category_thumbnail){
        $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
        return $mediaUrl.$this->_imageUploader->getBasePath().'/'.$widget_category_thumbnail;
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

    public function getSizeThumbnail()
    {
        if (is_readable($this->_geDir.'/category_thumbnail.xml'))
        {
            $data_sizes = $this->_parser->load($this->_geDir.'/category_thumbnail.xml')->xmlToArray();
            $common = [];
            $type_view = $this->getDataWidgetConfig('type_name');
            $type_template = 'default';
            if(isset($data_sizes['root'][$type_view]['common'])){
                $common = $data_sizes['root'][$type_view]['common'];
            }
            $custom = [];
            if($this->getDataWidgetConfig('type_name') == 'carousel'){
                $type_template = $this->getDataWidgetConfig('template_carousel_id') ? $this->getDataWidgetConfig('template_carousel_id') : 'carousel';
            }
            else{
                $type_template = $this->getDataWidgetConfig('template_id') ? $this->getDataWidgetConfig('template_id') : 'default';
            }
            if(isset($data_sizes['root'][$type_view][$type_template])){
                $custom = $data_sizes['root'][$type_view][$type_template];
            }
            return ['common' => $common, 'custom' => $custom];
        }
        return [];
    }
}