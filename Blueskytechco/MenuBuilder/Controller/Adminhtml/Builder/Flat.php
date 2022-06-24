<?php

namespace Blueskytechco\MenuBuilder\Controller\Adminhtml\Builder;

use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Blueskytechco\MenuBuilder\Model\MenuBuilderFactory;
use Blueskytechco\MenuBuilder\Model\MenuBuilderItemFactory;
use Blueskytechco\MenuBuilder\Model\MenuBuilderItemMetaFactory;
use Blueskytechco\MenuBuilder\ViewModel\MenuBuilder;

class Flat extends \Magento\Backend\App\Action
{
    
    protected $fileFactory;

    protected $_parser;

    protected $config;

    protected $menuBuilderFactory;

    protected $menuBuilderItemFactory;

    protected $menuBuilderItemMetaFactory;

    protected $menuBuilder;

    protected $_objectManager;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    protected $_layoutManager;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        Filesystem $filesystem,
        MenuBuilderFactory $menuBuilderFactory,
        MenuBuilderItemFactory $menuBuilderItemFactory,
        MenuBuilderItemMetaFactory $menuBuilderItemMetaFactory,
        MenuBuilder $MenuBuilder,
        \Magento\Framework\View\LayoutInterface $layoutManager,
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\PageCache\Model\Config $config
    ) {
        parent::__construct($context);
        $this->config = $config;
        $this->menuBuilderFactory = $menuBuilderFactory;
        $this->menuBuilderItemFactory = $menuBuilderItemFactory;
        $this->menuBuilderItemMetaFactory = $menuBuilderItemMetaFactory;
        $this->menuBuilder = $MenuBuilder;
        $this->_layoutManager = $layoutManager;
        $this->_objectManager = $objectmanager;
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
        $resultRedirect = $this->resultRedirectFactory->create();
        $resourceMenus = $this->menuBuilderFactory->create()->getResource();
        $view_model = $this->_objectManager->create('Blueskytechco\MenuBuilder\ViewModel\MenuBuilder');
        $data = $this->getRequest()->getParams();
        if (isset($data['id']) && $data['id']) {
            $menu_id = $data['id'];
            $menu = $resourceMenus->getMenuBuilderById($menu_id);
            if (isset($menu['entity_id']) && isset($menu['identifier'])) {
                $menu_id = $menu['entity_id'];
                $identifier = $menu['identifier'];
                $resourceMenus->deleteFlatData($menu_id); 
            }
            $this->messageManager->addSuccess(__('Run Flat Data Menu Success.'));
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        } else {
            $menus = $resourceMenus->getMenus();
            foreach ($menus as $menu) {
                if (isset($menu['entity_id']) && isset($menu['identifier'])) {
                    $menu_id = $menu['entity_id'];
                    $identifier = $menu['identifier'];
                    $resourceMenus->deleteFlatData($menu_id);
                }
            }    
            $this->messageManager->addSuccess(__('Run Flat Data Menu Success.'));
            $this->_redirect('adminhtml/system_config/edit/section/menus');
        }
    }
}
?>