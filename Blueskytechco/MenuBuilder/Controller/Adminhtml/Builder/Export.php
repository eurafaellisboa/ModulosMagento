<?php

namespace Blueskytechco\MenuBuilder\Controller\Adminhtml\Builder;

use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Blueskytechco\MenuBuilder\Model\MenuBuilderFactory;
use Blueskytechco\MenuBuilder\Model\MenuBuilderItemFactory;
use Blueskytechco\MenuBuilder\Model\MenuBuilderItemMetaFactory;

class Export extends \Magento\Backend\App\Action
{
    
    protected $fileFactory;

    protected $_parser;

    protected $config;

    protected $menuBuilderFactory;

    protected $menuBuilderItemFactory;

    protected $menuBuilderItemMetaFactory;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        Filesystem $filesystem,
        MenuBuilderFactory $menuBuilderFactory,
        MenuBuilderItemFactory $menuBuilderItemFactory,
        MenuBuilderItemMetaFactory $menuBuilderItemMetaFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\PageCache\Model\Config $config
    ) {
        parent::__construct($context);
        $this->config = $config;
        $this->menuBuilderFactory = $menuBuilderFactory;
        $this->menuBuilderItemFactory = $menuBuilderItemFactory;
        $this->menuBuilderItemMetaFactory = $menuBuilderItemMetaFactory;
		$this->_importPath = BP. '/' . DirectoryList::PUB . '/' . DirectoryList::MEDIA . '/menu_importer/';
        $this->logDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
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
        $data = $this->getRequest()->getParams();
        $resultRedirect = $this->resultRedirectFactory->create();
        $resourceMenus = $this->menuBuilderFactory->create()->getResource();
        $resourceMenusItem = $this->menuBuilderItemFactory->create()->getResource();
        $resourceMenusItemMeta =  $this->menuBuilderItemMetaFactory->create()->getResource();
        $id = '';
        if (isset($data['id']) && $data['id']) {
            $id = $data['id'];
            $menu_builder = $resourceMenus->getMenuBuilderById($id);
            if (!$menu_builder) {
                $message = __('Menu does not exist.');
                $this->messageManager->addErrorMessage($message);
                return $resultRedirect->setPath('*/*/index');
            }
            $fileName = 'menu_builder_'.$id.'.xml';
            $directoryPath = 'menu_importer';
            $directory = $this->logDirectory->isDirectory($directoryPath);
            
            if (!$directory) {
                $this->logDirectory->create($directoryPath);
            }
            $dom = $this->_parser->getDom();
            $dom->formatOutput = true;
            $root = $dom->createElement('root');
            $menus = $dom->createElement('menus');
            $get_menu_key = array('identifier', 'name', 'type');
            foreach ($menu_builder as $key => $menu_data) {
                if (!in_array($key, $get_menu_key)) {
                    continue;
                }
                $element_menu = $dom->createElement($key);
                $content_menu = $dom->createCDATASection($menu_data);
                $element_menu->appendChild($content_menu);
                $menus->appendChild($element_menu);
            }
            $menus_item = $resourceMenusItem->getMenuBuilderItemById($id);
            $get_item_key = array('entity_id', 'menu_order', 'item_title', 'type_menu', 'menu_object_id', 'status');
            $get_item_meta_key = array('meta_key', 'meta_value');
            foreach ($menus_item as $item_data) {
                $menu_item = $dom->createElement('item');
                foreach ($item_data as $key => $value) {
                    if (!in_array($key, $get_item_key)) {
                        continue;
                    }

                    $element_item = $dom->createElement($key);
                    $content_item = $dom->createCDATASection($value);
                    $element_item->appendChild($content_item);
                    $menu_item->appendChild($element_item);
                }
                $menus->appendChild($menu_item);

                $menus_item_meta = $resourceMenusItemMeta->getMenuBuilderItemMetaById($item_data['entity_id']);
                
                foreach ($menus_item_meta as $item_meta_data) {
                    $item_meta = $dom->createElement('meta');
                    foreach ($item_meta_data as $key => $value_meta) {
                        if (!in_array($key, $get_item_meta_key)) {
                            continue;
                        }

                        $element_item_meta = $dom->createElement($key);
                        $content_item_meta = $dom->createCDATASection($value_meta);
                        $element_item_meta->appendChild($content_item_meta);
                        $item_meta->appendChild($element_item_meta);
                    }
                    $menu_item->appendChild($item_meta);
                }

            }
            $root->appendChild($menus);
            $dom->appendChild($root);
            $content = $dom->saveXML();
            $dom->save($this->_importPath . $fileName);
            $this->messageManager->addSuccess(__('Exported Success.'));
            header('Content-Type: text/xml');
            header('Content-Disposition: attachment; filename="'.$fileName.'"'); 
            header('Content-Transfer-Encoding: binary');
            readfile($this->_importPath . $fileName);
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }
    }
}
?>