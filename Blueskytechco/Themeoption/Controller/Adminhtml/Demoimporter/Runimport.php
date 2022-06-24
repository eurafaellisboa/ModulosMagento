<?php

namespace Blueskytechco\Themeoption\Controller\Adminhtml\Demoimporter;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\PageCache\Version;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Cache\Frontend\Pool;
use Nwdthemes\Revslider\Helper\Data;
use Nwdthemes\Revslider\Model\Revslider\Admin\Includes\RevSliderFolder;
use \Nwdthemes\Revslider\Model\Revslider\Admin\Includes\RevSliderSliderImport;
use \Nwdthemes\Revslider\Model\Revslider\Admin\Includes\RevSliderTemplate;
use \Nwdthemes\Revslider\Helper\Query;
use \Nwdthemes\Revslider\Model\FrameworkAdapter as FA;
use Nwdthemes\Revslider\Model\Revslider\Front\RevSliderFront;
use Nwdthemes\Revslider\Model\Revslider\RevSliderSlider;
use Blueskytechco\MenuBuilder\Model\MenuBuilderFactory;
use Blueskytechco\MenuBuilder\Model\MenuBuilderItemFactory;
use Blueskytechco\MenuBuilder\Model\MenuBuilderItemMetaFactory;
use Blueskytechco\MenuBuilder\Model\ResourceModel\MenuBuilder\CollectionFactory;

class Runimport extends \Magento\Backend\App\Action
{
    protected $productSetFactory;
    protected $menuBuilderFactory;
    protected $menuBuilderItemFactory;
    protected $menuBuilderItemMetaFactory;
    protected $collectionMenuBuilderFactory;
    protected $fileFactory;
    protected $_parser;
    protected $resultJsonFactory;
    protected $_config;
    protected $cacheTypeList;
	protected $cacheFrontendPool;
	protected $indexFactory;
   	protected $indexCollection;
   	protected $_getFile;
	protected $_geDir;
	protected $_css;
    protected $_themeoptionversion;
    protected $_verifypurchasecode;
    protected $_fileDriver;
    private $framework;
    private $revSliderFunctions;
    private $revSliderAdmin;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Nwdthemes\Revslider\Helper\Framework $framework,
        \Nwdthemes\Revslider\Model\Revslider\RevSliderFunctions $revSliderFunctions,
        \Nwdthemes\Revslider\Model\Revslider\Admin\RevSliderAdmin $revSliderAdmin,
        \Magento\Framework\Filesystem\Driver\File $fileDriver,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\App\Config\ConfigResource\ConfigInterface $config,
        \Magento\Indexer\Model\IndexerFactory $indexFactory,
 		\Magento\Indexer\Model\Indexer\CollectionFactory $indexCollection,
 		\Magento\Framework\Filesystem $file,
 		\Blueskytechco\Themeoption\Model\Custom\Generator $css,
 		\Blueskytechco\Themeoption\Model\ThemeoptionversionFactory $themeoptionversion,
        \Blueskytechco\Themeoption\Helper\Verifypurchasecode $verifypurchasecode,
        \Blueskytechco\SetProduct\Model\ProductSetFactory $productSetFactory,
        MenuBuilderFactory $menuBuilderFactory,
        MenuBuilderItemFactory $menuBuilderItemFactory,
        MenuBuilderItemMetaFactory $menuBuilderItemMetaFactory,
        CollectionFactory $collectionMenuBuilderFactory,
        TypeListInterface $cacheTypeList, 
    	Pool $cacheFrontendPool
    ) {
        parent::__construct($context);
        $this->productSetFactory = $productSetFactory;
        $this->menuBuilderFactory = $menuBuilderFactory;
        $this->menuBuilderItemFactory = $menuBuilderItemFactory;
        $this->menuBuilderItemMetaFactory = $menuBuilderItemMetaFactory;
        $this->collectionMenuBuilderFactory = $collectionMenuBuilderFactory;
        $this->_fileDriver = $fileDriver;
        $this->framework = $framework;
        $upload_dir = $this->framework->wp_upload_dir();
        $this->remove_path = $upload_dir['basedir'].'/rstemp/';
        $this->revSliderFunctions = $revSliderFunctions;
        $this->revSliderAdmin = $revSliderAdmin;
        $this->_verifypurchasecode = $verifypurchasecode;
        $this->fileFactory = $fileFactory;
		$this->_importPath = BP. '/' . DirectoryList::PUB . '/' . DirectoryList::MEDIA . '/demo_importer/';
		$this->_parser = new \Magento\Framework\Xml\Parser();
		$this->_config = $config;
		$this->resultJsonFactory = $resultJsonFactory;
		$this->cacheTypeList = $cacheTypeList;
    	$this->cacheFrontendPool = $cacheFrontendPool;
    	$this->indexFactory = $indexFactory;
        $this->indexCollection = $indexCollection;
        $this->_getFile = $file;
		$this->_geDir = $this->_getFile->getDirectoryRead(DirectoryList::APP)->getAbsolutePath('code/Blueskytechco/Themeoption');
		$this->_css = $css;
        $this->_themeoptionversion = $themeoptionversion;
    }

    public function execute()
    {
    	$resultJson = $this->resultJsonFactory->create();

        if($this->_verifypurchasecode->checkEnvatoPurchaseCode()){
            $id = $this->getRequest()->getPost('id');
            $themename = $this->getRequest()->getPost('themename');
            $themetitle = $this->getRequest()->getPost('themetitle');
            $themeid = $this->getRequest()->getPost('themeid');
            $store = $this->getRequest()->getPost('store');
            $website = $this->getRequest()->getPost('website');

            $files_menu = scandir($this->_geDir.'/demo/menu');
            foreach($files_menu as $f) {
                if ($f != '.' && $f != '..'){
                    $xmlPathMenu = $this->_geDir.'/demo/menu/'.$f;
                    if (is_readable($xmlPathMenu))
                    {
                        $data_file_import = $this->_parser->load($xmlPathMenu)->xmlToArray();
                        if(isset($data_file_import['root']['menus'])){
                            $menus_data = $data_file_import['root']['menus'];
                            $collectionMenu = $this->collectionMenuBuilderFactory->create();
                            $collectionMenu->addFieldToFilter('identifier', ['eq' => $menus_data['identifier']]);
                            if($collectionMenu->count() == 0){
                                $array_child_item = [];
                                $menu_name = (isset($menus_data['name']) && $menus_data['name'])
                                    ? $menus_data['name']
                                    : __('Menu Name');
                                $menu_type = (isset($menus_data['type']) && $menus_data['type'])
                                    ? $menus_data['type']
                                    : 1;
                                $menus = $this->menuBuilderFactory->create();
                                $menus->setIdentifier($menus_data['identifier']);
                                $menus->setName($menu_name);
                                $menus->setType($menu_type);
                                $menus->save();
                                $menus_id = $menus->getEntityId();
                                if (isset($menus_data['item'])) {
                                    $items = $menus_data['item'];
                                    foreach ($items as $item) {
                                        if (
                                            !isset($item['menu_order']) || !isset($item['type_menu']) ||
                                            !isset($item['menu_object_id']) || !isset($item['status']) ||
                                            !isset($item['item_title'])
                                        ){
                                            continue;
                                        }
                                        $menus_item = $this->menuBuilderItemFactory->create();
                                        $menus_item->setMenuId($menus_id);    
                                        $menus_item->setItemTitle($item['item_title']);
                                        $menus_item->setMenuOrder($item['menu_order']);
                                        $menus_item->setTypeMenu($item['type_menu']);
                                        $menus_item->setMenuObjectId($item['menu_object_id']);
                                        $menus_item->setStatus($item['status']);
                                        $menus_item->save();
                                        $menus_item_id = $menus_item->getEntityId();
                                        $array_child_item[$item['entity_id']] = $menus_item_id;
                                        if (isset($item['meta'])) {
                                            $item_metas = $item['meta'];
                                            foreach ($item_metas as $meta) {
                                                if (
                                                    !isset($meta['meta_key']) || !isset($meta['meta_value'])
                                                ){
                                                    continue;
                                                }
                                                $menu_item_meta = $this->menuBuilderItemMetaFactory->create();
                                                $menu_item_meta->setMenuItemId($menus_item_id);
                                                if ($meta['meta_key'] == 'data_db_id') {
                                                    $meta_value = $menus_item_id;
                                                } elseif ($meta['meta_key'] == 'parent_id') {
                                                    $key_parent_id = $meta['meta_value'];
                                                    $meta_value = ($key_parent_id > 0 && isset($array_child_item[$key_parent_id]))
                                                        ? $array_child_item[$key_parent_id]
                                                        : 0;
                                                } else {
                                                    $meta_value = $meta['meta_value'];
                                                }
                                                $menu_item_meta->setMetaKey($meta['meta_key']);
                                                $menu_item_meta->setMetaValue($meta_value);
                                                $menu_item_meta->save();
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $xmlPathLookbook = $this->_geDir.'/demo/lookbook.xml';
            if (is_readable($xmlPathLookbook))
            {
                $dataLookbook = $this->_parser->load($xmlPathLookbook)->xmlToArray();
                if(isset($dataLookbook['root']['lookbook']['item'])){
                    foreach($dataLookbook['root']['lookbook']['item'] as $_item) {
                        $collection = $this->_objectManager->create('Blueskytechco\SetProduct\Model\ProductSet')->getCollection();
                        $collection->addFieldToFilter('identifier', ['eq' => $_item['identifier']]);
                        if(!$collection->getSize()){
                            $productSet = $this->productSetFactory->create();
                            $productSet->setName($_item['name']);
                            $productSet->setIdentifier($_item['identifier']);
                            $productSet->setTitle($_item['title']);
                            $productSet->setTitleLink($_item['title_link']);
                            $productSet->setButtonStyle($_item['button_style']);
                            $productSet->setWidth($_item['width']);
                            $productSet->setHeight($_item['height']);
                            $productSet->setBannerImage($_item['banner_image']);
                            $productSet->setProductData($_item['product_data']);
                            $productSet->save();   
                        }
                    }
                }
            }

            $xmlPathPage = $this->_geDir.'/demo/cms_pages.xml';
            if (is_readable($xmlPathPage))
            {
                $dataPage = $this->_parser->load($xmlPathPage)->xmlToArray();
                if(isset($dataPage['root']['pages']['item'])){
                    foreach($dataPage['root']['pages']['item'] as $_item) {
                        if(isset($_item['identifier']) && (strpos($_item['identifier'], 'demo_') === false || $_item['identifier'] == $themename)){
                            $collection = $this->_objectManager->create('Magento\Cms\Model\Page')->getCollection();
                            $collection->addFieldToFilter('identifier', $_item['identifier']);
                            if(!$collection->getSize()){
                                $page = $this->_objectManager->create('Magento\Cms\Model\Page');
                                $_item['store_id'] = array(0);
                                $page->addData($_item)->save();
                            }
                        }
                    }
                }
            }

            $xmlPathBlocks = $this->_geDir.'/demo/cms_blocks.xml';
            if (is_readable($xmlPathBlocks))
            {
                $dataBlocks = $this->_parser->load($xmlPathBlocks)->xmlToArray();
                if(isset($dataBlocks['root']['blocks']['item'])){
                    foreach($dataBlocks['root']['blocks']['item'] as $_itemblock) {
                        if(isset($_itemblock['identifier']) && (strpos($_itemblock['identifier'], 'demo_') === false || strpos($_itemblock['identifier'], $themename) !== false)){
                            $collection = $this->_objectManager->create('Magento\Cms\Model\Block')->getCollection();
                            $collection->addFieldToFilter('identifier', $_itemblock['identifier']);
                            if(!$collection->getSize()){
                                $block = $this->_objectManager->create('Magento\Cms\Model\Block');
                                $_itemblock['store_id'] = array(0);
                                $block->addData($_itemblock)->save();
                            }
                        }
                    }
                }
            }

            $scope = 'default';
            $scope_id = 0;
            $website_id = false;
            $store_id = false;
            if($website != 'default'){
                $scope = 'websites';
                $scope_id = $website;
                $website_id = $website;
            }
            if($store != 'default'){
                $scope = 'stores';
                $scope_id = $store;
                $store_id = $store;
            }
            $this->_config->saveConfig('web/default/cms_home_page',$themename,$scope,$scope_id);
            $this->_config->saveConfig('design/theme/theme_id',$themeid,$scope,$scope_id);
            if (is_readable($this->_geDir.'/demo/'.$themename.'.xml'))
            {
                $data_config = $this->_parser->load($this->_geDir.'/demo/'.$themename.'.xml')->xmlToArray();
                if(isset($data_config['config']['default']) && is_array($data_config['config']['default']) && !empty($data_config['config']['default'])){
                    foreach($data_config['config']['default'] as $key_config => $val_config) {
                        if(is_array($val_config) && !empty($val_config)){
                            foreach ($val_config as $key_child => $val_child) {
                                if(is_array($val_child) && !empty($val_child)){
                                    foreach ($val_child as $key_child2 => $val_child2) {
                                        $this->_config->saveConfig($key_config.'/'.$key_child.'/'.$key_child2,$val_child2,$scope,$scope_id);
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $rivo = $this->_importPath . 'revslider/'.$themename.'.zip';
            if ($this->_fileDriver->isExists($rivo)) {
                $re = new RevSliderSlider();
                if(!$re->isAliasExistsInDB($themename)){
                    $data = $this->revSliderFunctions->get_request_var('data');
                    $data = ($data == '') ? array() : $data;
                    $import = new RevSliderSliderImport();
                    $return = $import->import_slider(true, $rivo);
                    if($this->revSliderFunctions->get_val($return, 'success') == true){
                        $new_id = $this->revSliderFunctions->get_val($return, 'sliderID');

                        if(intval($new_id) > 0){
                            $folder = new RevSliderFolder();
                            $folder_id = $this->revSliderFunctions->get_val($data, 'folderid', -1);
                            if(intval($folder_id) > 0){
                                $folder->add_slider_to_folder($new_id, $folder_id, false);
                            }
                        }
                    }
                }
            }

            $_types = [
                'config',
                'layout',
                'block_html',
                'full_page'
            ];

            foreach ($_types as $type) {
                $this->cacheTypeList->cleanType($type);
            }
            foreach ($this->cacheFrontendPool as $cacheFrontend) {
                $cacheFrontend->getBackend()->clean();
            }

            $indexidarray = $this->indexFactory->create()->load('design_config_grid');
            $indexidarray->reindexAll('design_config_grid');

            $t_option = $this->_themeoptionversion->create();
            $t_option->setVersion(strtotime('now'));
            $t_option->setVersionTime(date('Y-m-d H:i:s'));
            $t_option->save();
            $this->_css->generateCss($website_id, $store_id);
            
            $response = ['result' => 'success'];
        }
        else{
            $response = ['result' => 'error'];
        }

        return $resultJson->setData($response);
    }
}
?>