<?php

namespace Blueskytechco\Themeoption\Controller\Adminhtml\Exportoptions;

use Magento\Framework\App\Filesystem\DirectoryList;

class Exportconfiguration extends \Magento\Backend\App\Action
{
    protected $storeRepository;
    protected $_themeconfig;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Store\Api\StoreRepositoryInterface $storeRepository,
        \Blueskytechco\Themeoption\Helper\Themeconfig $themeconfig,
        \Magento\PageCache\Model\Config $config
    ) {
        parent::__construct($context);
        $this->config = $config;
        $this->fileFactory = $fileFactory;
        $this->storeRepository = $storeRepository;
		$this->_importPath = BP. '/' . DirectoryList::PUB . '/' . DirectoryList::MEDIA . '/demo_importer/';
		$this->_parser = new \Magento\Framework\Xml\Parser();
		$this->_themeconfig = $themeconfig;
    }

    public function execute()
    {
    	$stores = $this->storeRepository->getList();
    	$s = $this->getRequest()->getParam('store');
    	$fil = 'default.xml';

    	if($s){
    		foreach ($stores as $store) {
    			if($store->getId() == $s){
    				$fil = $store->getCode().'.xml';
    				break;
    			}
    		}
    	}

		$dom2 = $this->_parser->getDom();
		$dom2->formatOutput = true;
		$root2 = $dom2->createElement('config');
		$def = $dom2->createElement('default');
		$themesetting = $dom2->createElement('themesetting');

		$theme_setting = $this->_themeconfig->getConfiguration('themesetting', $s);
    	foreach($theme_setting as $key_setting => $val_setting){
    		$item_s = $dom2->createElement($key_setting);
    		foreach($val_setting as $key_s => $val_s)
			{

				$element_s = $dom2->createElement($key_s);
				$content_s = $dom2->createCDATASection($val_s);
				$element_s->appendChild($content_s);
				$item_s->appendChild($element_s);
			}
			$themesetting->appendChild($item_s);
    	}

		$def->appendChild($themesetting);

		$theme_option = $this->_themeconfig->getConfiguration('themeoption', $s);
		$themeoption = $dom2->createElement('themeoption');
		foreach($theme_option as $key_option => $val_option){
    		$item_op = $dom2->createElement($key_option);
    		foreach($val_option as $key_o => $val_o)
			{

				$element_o = $dom2->createElement($key_o);
				$content_o = $dom2->createCDATASection($val_o);
				$element_o->appendChild($content_o);
				$item_op->appendChild($element_o);
			}
			$themeoption->appendChild($item_op);
    	}
		$def->appendChild($themeoption);


		$mf_blog = $this->_themeconfig->getConfiguration('mfblog', $s);
		$mfblog = $dom2->createElement('mfblog');
		foreach($mf_blog as $key_mfblog => $val_mfblog){
			$item_mfblog = $dom2->createElement($key_mfblog);
			foreach($val_mfblog as $key_m => $val_m)
			{
				if(!is_array($val_m)){
					$element_m = $dom2->createElement($key_m);
					$content_m = $dom2->createCDATASection($val_m);
					$element_m->appendChild($content_m);
					$item_mfblog->appendChild($element_m);
				}
			}
			$mfblog->appendChild($item_mfblog);
		}
		$def->appendChild($mfblog);

		$instagram_section = $this->_themeconfig->getConfiguration('instagramsection', $s);
		$instagramsection = $dom2->createElement('instagramsection');
		foreach($instagram_section as $key_instagramsection => $val_instagramsection){
			$item_instagramsection = $dom2->createElement($key_instagramsection);
			foreach($val_instagramsection as $key2_instagramsection => $val2_instagramsection)
			{

				$element2_instagramsection = $dom2->createElement($key2_instagramsection);
				$content2_instagramsection = $dom2->createCDATASection($val2_instagramsection);
				$element2_instagramsection->appendChild($content2_instagramsection);
				$item_instagramsection->appendChild($element2_instagramsection);
			}
			$instagramsection->appendChild($item_instagramsection);
		}
		$def->appendChild($instagramsection);

		$me_nus = $this->_themeconfig->getConfiguration('menus', $s);
		$menus = $dom2->createElement('menus');
		foreach($me_nus as $key_menus => $val_menus){
			$item_menus = $dom2->createElement($key_menus);
			foreach($val_menus as $key2_menus => $val2_menus)
			{

				$element2_menus = $dom2->createElement($key2_menus);
				$content2_menus = $dom2->createCDATASection($val2_menus);
				$element2_menus->appendChild($content2_menus);
				$item_menus->appendChild($element2_menus);
			}
			$menus->appendChild($item_menus);
		}
		$def->appendChild($menus);

		$ajax_suite = $this->_themeconfig->getConfiguration('ajaxsuite', $s);
		$ajaxsuite = $dom2->createElement('ajaxsuite');
		foreach($ajax_suite as $key_ajaxsuite => $val_ajaxsuite){
			$item_ajaxsuite = $dom2->createElement($key_ajaxsuite);
			foreach($val_ajaxsuite as $key2_ajaxsuite => $val2_ajaxsuite)
			{
				if($key_ajaxsuite == 'product_suggestion' && $key2_ajaxsuite == 'category'){
					continue;
				}
				else{
					if($key_ajaxsuite == 'product_suggestion' && $key2_ajaxsuite == 'enable'){
						$element2_ajaxsuite = $dom2->createElement($key2_ajaxsuite);
						$content2_ajaxsuite = $dom2->createCDATASection('0');
					}
					else{
						$element2_ajaxsuite = $dom2->createElement($key2_ajaxsuite);
						$content2_ajaxsuite = $dom2->createCDATASection($val2_ajaxsuite);
					}
					$element2_ajaxsuite->appendChild($content2_ajaxsuite);
					$item_ajaxsuite->appendChild($element2_ajaxsuite);
				}
			}
			$ajaxsuite->appendChild($item_ajaxsuite);
		}
		$def->appendChild($ajaxsuite);

		$quickview_product = $this->_themeconfig->getConfiguration('quickview_product', $s);
		$quickviewproduct = $dom2->createElement('quickview_product');
		foreach($quickview_product as $key_quickviewproduct => $val_quickviewproduct){
			$item_quickviewproduct = $dom2->createElement($key_quickviewproduct);
			foreach($val_quickviewproduct as $key2_quickviewproduct => $val2_quickviewproduct)
			{

				$element2_quickviewproduct = $dom2->createElement($key2_quickviewproduct);
				$content2_quickviewproduct = $dom2->createCDATASection($val2_quickviewproduct);
				$element2_quickviewproduct->appendChild($content2_quickviewproduct);
				$item_quickviewproduct->appendChild($element2_quickviewproduct);
			}
			$quickviewproduct->appendChild($item_quickviewproduct);
		}
		$def->appendChild($quickviewproduct);

		$search_suite = $this->_themeconfig->getConfiguration('searchsuite', $s);
		$searchsuite = $dom2->createElement('searchsuite');
		foreach($search_suite as $key_searchsuite => $val_searchsuite){
			$item_searchsuite = $dom2->createElement($key_searchsuite);
			foreach($val_searchsuite as $key2_searchsuite => $val2_searchsuite)
			{

				$element2_searchsuite = $dom2->createElement($key2_searchsuite);
				$content2_searchsuite = $dom2->createCDATASection($val2_searchsuite);
				$element2_searchsuite->appendChild($content2_searchsuite);
				$item_searchsuite->appendChild($element2_searchsuite);
			}
			$searchsuite->appendChild($item_searchsuite);
		}
		$def->appendChild($searchsuite);

		$root2->appendChild($def);
		$dom2->appendChild($root2);
		$content2 = $dom2->saveXML();
		$dom2->save($this->_importPath . $fil);
		$this->messageManager->addSuccess(__('Configuration Exported.'));
        $this->_redirect('adminhtml/system_config/edit/section/exportoptions');
    }
}
?>