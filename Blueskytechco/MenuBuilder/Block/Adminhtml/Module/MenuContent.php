<?php
/**
 * Copyright © 2021 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Blueskytechco\MenuBuilder\Block\Adminhtml\Module;

use Blueskytechco\MenuBuilder\Model\MenuBuilderFactory;
use Blueskytechco\MenuBuilder\Model\MenuBuilderItemFactory;
use Blueskytechco\MenuBuilder\Model\MenuBuilderItemMetaFactory;
use Magento\Cms\Model\PageFactory;
use Blueskytechco\MenuBuilder\Helper\Admin as MenuHelper;
use Magento\Cms\Model\ResourceModel\Page\CollectionFactory as PageCollection;

class MenuContent extends \Magento\Backend\Block\Template
{
    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'menu_content.phtml';

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Catalog\Helper\Category
     */
    protected $_categoryHelper;
    
    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $_categoryFactory;
    
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlInterface;
    
    /**
     * @var \Magento\Catalog\Model\Indexer\Category\Flat\State
     */
    protected $_categoryFlatConfig;
    
    /**
     * @var \Blueskytechco\MenuBuilder\Model\MenuBuilderFactory
     */
    protected $menuBuilderFactory;
    
    /**
     * @var \Blueskytechco\MenuBuilder\Model\MenuBuilderItemMetaFactory
     */
    protected $menuBuilderItemMetaFactory;
    
    /**
     * @var \Blueskytechco\MenuBuilder\Model\ResourceModel\MenuBuilderItem\CollectionFactory
     */
    protected $collectionMenuItemFactory;
    
    /**
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $pageFactory;
    
    /**
     * @var \Magento\Catalog\Model\CategoryRepository
     */
    protected $_categoryRepository;
    
    /**
     * @var MenuHelper
     */
    protected $menuHelper;
    
    /**
     * @var PageCollection
     */
    protected $pageCollection;
    
    /**
     * AssignProducts constructor.
     *
     * @param \Magento\Backend\Block\Template\Context  $context
     * @param array                                    $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Backend\Model\Url $backendUrlManager,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        MenuBuilderFactory $menuBuilderFactory,
        MenuBuilderItemFactory $menuBuilderItemFactory,
        MenuBuilderItemMetaFactory $menuBuilderItemMetaFactory,
        MenuHelper $menuHelper,
        \Magento\Catalog\Model\Indexer\Category\Flat\State $categoryFlatState,
        \Magento\Catalog\Helper\Category $categoryHelper,
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        \Blueskytechco\MenuBuilder\Model\ResourceModel\MenuBuilderItem\CollectionFactory $collectionMenuItemFactory,
        PageFactory $pageFactory,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        PageCollection $pageCollection,
        array $data = []
    ) {
        $this->_categoryHelper = $categoryHelper;
        $this->menuBuilderFactory = $menuBuilderFactory;
        $this->menuBuilderItemFactory = $menuBuilderItemFactory;
        $this->menuBuilderItemMetaFactory = $menuBuilderItemMetaFactory;
        $this->_objectManager = $objectmanager;
        $this->_categoryFactory = $categoryFactory;
        $this->_urlInterface = $urlInterface;
        $this->menuHelper = $menuHelper;
        $this->_categoryFlatConfig = $categoryFlatState;
        $this->backendUrlManager  = $backendUrlManager;
        $this->collectionMenuItemFactory = $collectionMenuItemFactory;
        $this->pageFactory = $pageFactory;
        $this->_categoryRepository = $categoryRepository;
        $this->_pageCollection = $pageCollection;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve stores collection with default store
     */
    public function getDataContent()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $patternModel = $this->menuBuilderFactory->create()->load($id);
            return $patternModel;
        } else {
            return false;
        }
    }
    
    public function getJsonConfigWysiwyg()
    {
        return $this->menuHelper->getJsonConfigWysiwyg();
    }
    
    public function getResourceMenuItem()
    {
        $resource = $this->menuBuilderItemFactory->create()->getResource();
        return $resource;
    }
    
    public function getListCmsPageHtml()
    {
        $collection = $this->_pageCollection->create();
        $collection->addFieldToFilter('is_active', \Magento\Cms\Model\Page::STATUS_ENABLED);
        $pages = '';
        foreach ($collection as $key => $page) {
            $pages .= '<li class="ui-menu-item">';
                $pages .= '<div class="menu-item">';
                    $pages .= '<label class="menu-item-title">';
                        $pages .= '<input type="checkbox" class="menu-item-checkbox" 
                            name="menu-item[menu-item-object-id]" value="'.$page->getId().'">';
                        $pages .= ''.$page->getTitle().'';
                    $pages .= '</label>';
                $pages .= '</div>';
            $pages .= '</li>';
        }
        return $pages;
    }
    
    public function getListCategoriesHtml()
    {
        $html = '';
        $menu_resource = $this->menuBuilderFactory->create()->getResource();
        $categories_root = $menu_resource->getRootCatalogId();
        if (!$categories_root) {
            return $html;
        }
        $categorie_roots = $menu_resource->getCategories($categories_root, 1);
        foreach ($categorie_roots as $key => $categorie_root) {
            $html .= '<li class="ui-menu-root">';
                $html .= '<div class="menu-root">';
                    $html .= '<label class="menu-item-title">';
                        $html .= ''.$categorie_root['name'].'';
                    $html .= '</label>';
                $html .= '</div>';
                $html .= '<ul class="root-list-menu" style="display:none">';
                $categories = $menu_resource->getCategories($categorie_root['entity_id'], 2);
                foreach ($categories as $key => $category) {
                    if (
                        (isset($category['is_active']) && isset($category['include_in_menu']))
                        && (!$category['is_active'] || !$category['include_in_menu'])
                    ) {
                        continue;
                    }
                    $children = $menu_resource->getCategories($category['entity_id'], 3);
                    $parent_id = $category['entity_id'];
                    $html .= '<li class="ui-menu-item">';
                        $html .= '<div class="menu-item">';
                            $html .= '<label class="menu-item-title">';
                                $html .= '<input type="checkbox" class="menu-item-checkbox category'.$category['entity_id'].'" 
                                    name="menu-item[menu-item-object-id]" data-parent_id="0" value="'.$category['entity_id'].'">';
                                $html .= ''.$category['name'].'';
                            $html .= '</label>';
                    if (count($children) > 0) {
                        $html .= '<div class="open-children-toggle"></div>';
                    }
                        $html .= '</div>';
                    if (count($children) > 0) {
                        $html .= '<div class="menu-item-sub" style="display:none">';
                            $html .= $this->getSubmenuItems($children, $parent_id, 4);
                        $html .= '</div>';
                    }
                    $html .= '</li>';
                }
                $html .= '</ul>';
            $html .= '</li>';
        }
        return $html;
    }
    
    public function getSubmenuItems($children, $parent_id, $level = 4)
    {
        $html = '';
        $menu_resource = $this->menuBuilderFactory->create()->getResource();
        $margin = $level*10;
        $html = '<ul class="subchildmenu" style="margin-left:'.$margin.'px">';
        foreach ($children as $key => $child) {
            if (
                (isset($child['is_active']) && isset($child['include_in_menu']))
                && (!$child['is_active'] || !$child['include_in_menu'])
            ) {
                continue;
            }
            $sub_children = $menu_resource->getCategories($child['entity_id'], $level);
            $html .= '<li class="ui-menu-item">';
                $html .= '<div class="menu-item">';
                    $html .= '<label class="menu-item-title">';
                        $html .= '<input type="checkbox" 
                            class="menu-item-checkbox category'.$child['entity_id'].'" 
                            name="menu-item[menu-item-object-id]" 
                            data-parent_id="'.$parent_id.'" value="'.$child['entity_id'].'">';
                        $html .= ''.$child['name'].'';
                    $html .= '</label>';
            if (count($sub_children) > 0) {
                $html .= '<div class="open-children-toggle"></div>';
            }
                $html .= '</div>';
            if (count($sub_children) > 0) {
                $parent_child_id = $child['entity_id'];
                $html .= '<div class="menu-item-sub" style="display:none">';
                    $html .= $this->getSubmenuItems($sub_children, $parent_child_id, $level+1);
                $html .= '</div>';
            }
            $html .= '</li>';
        }
        $html .= '</ul>';
        return $html;
    }
    
    public function getContentHtml()
    {
        $html = '';
        $menu = $this->getDataContent();
        if ($menu) {
            $menu_id = $menu->getId();
            $connection = $this->collectionMenuItemFactory->create();
            $connection->addFilter('menu_id', ['in' => $menu_id])
                    ->addFilter('status', ['in' => 1])
                    ->setOrder('menu_order', 'asc');
            if ($connection->count() > 0) {
                foreach ($connection as $menu_item) {
                    $html .= $this->getMenuItem($menu_item, $menu_id);
                }
            }
        }
        return $html;
    }
    
    public function getMenuItem($menu_item, $menu_id)
    {
        $html = '';
        $type_menu = $menu_item->getTypeMenu();
        $item_object_id = $menu_item->getMenuObjectId();
        if ($type_menu == 'cmspage') {
            $cmspage = $this->pageFactory->create()->load($item_object_id);
            $title = ($menu_item->getItemTitle())
                ? $menu_item->getItemTitle()
                : $cmspage->getTitle();
            $type = __('Cms Page');
            $html .= $this->getMenuItemHtml($title, $type, $type_menu, $menu_item, $menu_id);
        } elseif ($type_menu == 'category') {
            $checkCategoryId = $this->getResourceMenuItem()->checkCategoryBuyId($item_object_id);
            if ($checkCategoryId) {
                $category = $this->_categoryRepository->get($item_object_id);
                $title = ($menu_item->getItemTitle()) ? $menu_item->getItemTitle() : $category->getName();
                $type = __('Category');
                $html .= $this->getMenuItemHtml($title, $type, $type_menu, $menu_item, $menu_id);
            }
        } else {
            $type = __('Custom Link');
            $title = ($menu_item->getItemTitle()) ? $menu_item->getItemTitle() : $type;
            $html .= $this->getMenuItemHtml($title, $type, $type_menu, $menu_item, $menu_id);
        }
        return $html;
    }
    
    public function getMenuItemHtml($title, $type, $type_menu, $menu_item, $menu_id)
    {
        $menu_item_id = $menu_item->getId();
        $depth = $this->getResourceMenuItem()->getDepthItem($menu_item_id, 0);
        $html = '';
        $html .= '<li id="menu-item-'.$menu_item_id.'" class="menu-item menu-item-depth-'.$depth.'">';
            $html .= '<div class="menu-item-bar">';
                $html .= '<div class="menu-item-handle">';
                    $html .= '<span class="item-title"><span class="menu-item-title">'.$title.'</span></span>';
                    $html .= '<div class="ui-sortable-handle">';
                        $html .= '<span class="item-controls">';
                            $html .= '<span class="item-type">'.$type.'</span>';
                            $html .= '<a class="item-edit" href="#">
                                <span class="screen-reader-text">'.__('Edit').'</span>
                                </a>';
                        $html .= '</span>';
                    $html .= '</div>';
                $html .= '</div>';
            $html .= '</div>';
            $html .= $this->menuHelper->getMenuItemSettingsHtml(
                $title,
                null,
                $type_menu,
                $menu_item_id,
                $menu_id,
                $depth,
                0
            );
            $html .= '<ul class="menu-item-transport" style=""></ul>';
        $html .= '</li>';
        return $html;
    }
}
