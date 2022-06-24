<?php

namespace Blueskytechco\Themeoption\Controller\Adminhtml\Exportoptions;

use Magento\Framework\App\Filesystem\DirectoryList;

class Slidebanner extends \Magento\Backend\App\Action
{
    protected $storeRepository;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Store\Api\StoreRepositoryInterface $storeRepository,
        \Magento\PageCache\Model\Config $config
    ) {
        parent::__construct($context);
        $this->config = $config;
        $this->fileFactory = $fileFactory;
        $this->storeRepository = $storeRepository;
		$this->_importPath = BP. '/' . DirectoryList::PUB . '/' . DirectoryList::MEDIA . '/demo_importer/';
		$this->_parser = new \Magento\Framework\Xml\Parser();
    }

    public function execute()
    {
    	$stores = $this->storeRepository->getList();

        $fileName = 'themes.xml';
   
		$dom = $this->_parser->getDom();
		$dom->formatOutput = true;
		$root = $dom->createElement('root');
		$themes = $dom->createElement('themes');

		$element_id = $dom->createElement('id');
		$content_id = $dom->createCDATASection('36891241');
		$element_id->appendChild($content_id);
		$themes->appendChild($element_id);

		$element_name = $dom->createElement('name');
		$content_name = $dom->createCDATASection('Kalles - Clean minimal Magento 2 theme');
		$element_name->appendChild($content_name);
		$themes->appendChild($element_name);

		$element_des = $dom->createElement('des');
		$content_des = $dom->createCDATASection('You need install theme before use importer. Importing demo data is the easiest way to setup your theme. It will allow you to quickly edit everything. You can select import theme for each store view. Reload storefront after imported.');
		$element_des->appendChild($content_des);
		$themes->appendChild($element_des);

		foreach ($stores as $store) {
			if($store->getCode() == 'admin'){
				continue;
			}
    		$item_s = $dom->createElement('home');
    		$arr = ['title' => $store->getName(),'identifier' => $store->getCode(),'image' => 'blueskytechmage.com/preview/kalles/'.$store->getCode().'.jpg','linkpreview' => 'blueskytechmage.com/kalles/'.$store->getCode()];
    		foreach($arr as $key_s => $val_s){
    			$element_s = $dom->createElement($key_s);
				$content_s = $dom->createCDATASection($val_s);
				$element_s->appendChild($content_s);
				$item_s->appendChild($element_s);
    		}
    		$themes->appendChild($item_s);
    	}
    	$root->appendChild($themes);
		$dom->appendChild($root);
		$content = $dom->saveXML();
		$dom->save($this->_importPath . $fileName);
		$this->messageManager->addSuccess(__('Configuration Exported.'));
        $this->_redirect('adminhtml/system_config/edit/section/exportoptions');
    }
}
?>