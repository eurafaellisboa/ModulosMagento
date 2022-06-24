<?php

namespace Blueskytechco\Themeoption\Controller\Adminhtml\Exportoptions;

use Magento\Framework\App\Filesystem\DirectoryList;

class Lookbook extends \Magento\Backend\App\Action
{
    
    protected $fileFactory;

    protected $_parser;

    protected $config;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\PageCache\Model\Config $config
    ) {
        parent::__construct($context);
        $this->config = $config;
		$this->_importPath = BP. '/' . DirectoryList::PUB . '/' . DirectoryList::MEDIA . '/demo_importer/';
        $this->fileFactory = $fileFactory;
		$this->_parser = new \Magento\Framework\Xml\Parser();
    }

    /**
     * Export Varnish Configuration as .vcl
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $fileName = 'lookbook.xml';
        
		$collection = $this->_objectManager->get('Blueskytechco\SetProduct\Model\ProductSet')->getCollection();
		$dom = $this->_parser->getDom();
		$dom->formatOutput = true;
		$root = $dom->createElement('root');
		$blocks = $dom->createElement('lookbook');
		$get_key = array('name', 'identifier', 'title', 'title_link', 'button_style', 'width', 'height', 'banner_image', 'product_data');
		foreach($collection as $block)
		{
			$item = $dom->createElement('item');
			foreach($block->getData() as $key=>$value)
			{
				if(!in_array($key, $get_key)){
					continue;
				}

				$element = $dom->createElement($key);
				$content = $dom->createCDATASection($value);
				$element->appendChild($content);
				$item->appendChild($element);
			}
			$blocks->appendChild($item);
		}
		$root->appendChild($blocks);
		$dom->appendChild($root);
		$content = $dom->saveXML();
		$dom->save($this->_importPath . $fileName);
		$this->messageManager->addSuccess(__('Lookbook Exported.'));
        $this->_redirect('adminhtml/system_config/edit/section/exportoptions');
    }
}
?>