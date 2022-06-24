<?php

namespace Blueskytechco\Themeoption\Controller\Adminhtml\Exportoptions;

use Magento\Framework\App\Filesystem\DirectoryList;

class Exportpage extends \Magento\Backend\App\Action
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
        $this->fileFactory = $fileFactory;
		$this->_importPath = BP. '/' . DirectoryList::PUB . '/' . DirectoryList::MEDIA . '/demo_importer/';
		$this->_parser = new \Magento\Framework\Xml\Parser();
    }

    public function execute()
    {
        $fileName = 'cms_pages.xml';
   
		$collection = $this->_objectManager->get('Magento\Cms\Model\Page')->getCollection();
		$dom = $this->_parser->getDom();
		$dom->formatOutput = true;
		$root = $dom->createElement('root');
		$blocks = $dom->createElement('pages');
		$get_key = array('title', 'page_layout', 'meta_keywords', 'meta_description', 'identifier', 'content_heading', 'content', 'meta_title');
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
		$this->messageManager->addSuccess(__('Elements Pages Exported.'));
        $this->_redirect('adminhtml/system_config/edit/section/exportoptions');
    }
}
?>