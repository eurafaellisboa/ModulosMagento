<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
 
namespace Blueskytechco\MenuBuilder\ViewModel;

use Blueskytechco\MenuBuilder\Model\MenuBuilderFactory;
use Blueskytechco\MenuBuilder\Model\MenuBuilderItemFactory;
use Blueskytechco\MenuBuilder\Model\MenuBuilderItemMetaFactory;
use Blueskytechco\MenuBuilder\Helper\Data as MenuHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Cms\Model\PageFactory;
use Magento\Catalog\Helper\Category;
use Blueskytechco\MenuBuilder\Helper\Category as CategoryHelper;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\View\Helper\SecureHtmlRenderer;
use Magento\Framework\Filter\DirectiveProcessor\LegacyDirective;
use Magento\Framework\Filter\Template;
use Magento\Framework\UrlInterface;

class MenuBuilder implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * @var MenuBuilderFactory
     */
    protected $menuBuilderFactory;
    
    /**
     * @var MenuBuilderItemFactory
     */
    protected $menuBuilderItemFactory;
    
    /**
     * @var MenuBuilderItemMetaFactory
     */
    protected $menuBuilderItemMetaFactory;
    
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var UrlInterface
     */
    private $_urlInterface;
    
    /**
     * @var MenuHelper
     */
    protected $helper;
    
    /**
     * @var CategoryFactory
     */
    protected $_categoryFactory;
    
    /**
     * @var PageFactory
     */
    protected $pageFactory;
    
    /**
     * @var Category
     */
    protected $_categoryHelper;
    
    /**
     * @var CategoryHelper
     */
    protected $_categoryMenuHelper;
    
    /**
     * @var FilterProvider
     */
    protected $_filterProvider;
    
    /**
     * @var SecureHtmlRenderer
     */
    protected $secureRenderer;

    /**
     * @var LegacyDirective
     */
    protected $legacyDirective;

    /**
     * @var Template
     */
    protected $template;

    /**
     * Assigned template variables
     *
     * @var array
     */
    protected $templateVars = [];

    /**
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        MenuBuilderFactory $menuBuilderFactory,
        MenuBuilderItemFactory $menuBuilderItemFactory,
        MenuBuilderItemMetaFactory $menuBuilderItemMetaFactory,
        StoreManagerInterface $storeManager,
        UrlInterface $urlInterface,
        MenuHelper $menuHelper,
        CategoryFactory $categoryFactory,
        PageFactory $pageFactory,
        Category $categoryHelper,
        CategoryHelper $categoryMenuHelper,
        FilterProvider $_filterProvider,
        LegacyDirective $legacyDirective,
        Template $template,
        SecureHtmlRenderer $secureRenderer
    ) {
        $this->menuBuilderFactory = $menuBuilderFactory;
        $this->menuBuilderItemFactory = $menuBuilderItemFactory;
        $this->menuBuilderItemMetaFactory = $menuBuilderItemMetaFactory;
        $this->storeManager = $storeManager;
        $this->helper = $menuHelper;
        $this->_urlInterface = $urlInterface;
        $this->_categoryFactory = $categoryFactory;
        $this->pageFactory = $pageFactory;
        $this->_categoryHelper = $categoryHelper;
        $this->_categoryMenuHelper = $categoryMenuHelper;
        $this->_filterProvider = $_filterProvider;
        $this->legacyDirective = $legacyDirective;
        $this->template = $template;
        $this->secureRenderer = $secureRenderer;
    }
    
    public function getCategoryModel($id)
    {
        $_category = $this->_categoryFactory->create();
        $_category->setStoreId($this->storeManager->getStore()->getId())->load($id);
        return $_category;
    }
    
    public function loadCMSPage($id)
    {
        $page = $this->pageFactory->create();
        $page->load($id);
        return $page;
    }
    
    public function getBlockContent($content = '')
    {
        if (!$this->_filterProvider) {
            return $content;
        }
        return $this->_filterProvider->getBlockFilter()->filter(trim($content));
    }
    
    public function getResourceMenuBuilderItem()
    {
        return $this->menuBuilderItemFactory->create()->getResource();
    }
    
    /**
     * Get horizontal menu html
     */
    public function getHorizontalMenuHtml()
    {
        $menu_id = $this->getData('menus/horizontal/horizontal_menu');
        $store_id = $this->storeManager->getStore()->getId();
        if (!$menu_id) {
            return false;
        }
        $resourceMenus = $this->menuBuilderFactory->create()->getResource();
        $menu_id = $resourceMenus->getMenuBuilderIdByIdentifier($menu_id);
        if (!$resourceMenus->checkMenuBuilder($menu_id, 1)) {
            return false;
        }
        $flat_data = $this->getData('menus/flat/flat_data');
        if ($flat_data) {
            $flat_html = $resourceMenus->getContentHtml($menu_id, $store_id);
            if ($flat_html) {
                return $flat_html;
            }
        }
        $menu_items = $this->getResourceMenuBuilderItem()->getItems($menu_id);
        $html = $this->getMenuHtml($menu_id, $menu_items);
        if ($flat_data) {
            $identifier = $resourceMenus->getIdentifier($menu_id);
            $resourceMenus->addContentFlatData($menu_id, $identifier, $store_id, $html);
        }
        return $html;
    }
    
    /**
     * Get vertical menu html
     */
    public function getVerticalMenuHtml()
    {
        $menu_id = $this->getData('menus/vertical/vertical_menu');
        $store_id = $this->storeManager->getStore()->getId();
        if (!$menu_id) {
            return false;
        }
        $resourceMenus = $this->menuBuilderFactory->create()->getResource();
        $menu_id = $resourceMenus->getMenuBuilderIdByIdentifier($menu_id);
        if (!$resourceMenus->checkMenuBuilder($menu_id, 2)) {
            return false;
        }
        $flat_data = $this->getData('menus/flat/flat_data');
        if ($flat_data) {
            $flat_html = $resourceMenus->getContentHtml($menu_id, $store_id);
            if ($flat_html) {
                return $flat_html;
            }
        }
        $menu_items = $this->getResourceMenuBuilderItem()->getItems($menu_id);
        $html = $this->getMenuHtml($menu_id, $menu_items);
        if ($flat_data) {
            $identifier = $resourceMenus->getIdentifier($menu_id);
            $resourceMenus->addContentFlatData($menu_id, $identifier, $store_id, $html);
        }
        return $html;
    }
    
    /**
     * Get Widget menu html
     */
    public function getWidgetMenuHtml($menu_id)
    {
        if (!$menu_id) {
            return false;
        }
        $store_id = $this->storeManager->getStore()->getId();
        $resourceMenus = $this->menuBuilderFactory->create()->getResource();
        $menu_id = $resourceMenus->getMenuBuilderIdByIdentifier($menu_id);
        if (!$resourceMenus->checkMenuBuilderWidget($menu_id)) {
            return false;
        }
        $flat_data = $this->getData('menus/flat/flat_data');
        if ($flat_data) {
            $flat_html = $resourceMenus->getContentHtml($menu_id, $store_id);
            if ($flat_html) {
                return $flat_html;
            }
        }
        $menu_items = $this->getResourceMenuBuilderItem()->getItems($menu_id);
        $html = $this->getMenuHtml($menu_id, $menu_items);
        if ($flat_data) {
            $identifier = $resourceMenus->getIdentifier($menu_id);
            $resourceMenus->addContentFlatData($menu_id, $identifier, $store_id, $html);
        }
        return $html;
    }
    
    /**
     * return html menu item
     */
    public function getMenuHtml($menu_id, $menu_items)
    {
        $html = '';
        $max_level = 5;
        foreach ($menu_items as $item) {
            if ($item['parent_id'] != 0) {
                continue;
            }
            $cssInline = '';
            // get config menu item.
            $icon_image = (isset($item['icon_image']) && $item['icon_image'])
                ? $item['icon_image']
                : '';
            $submenu_bg_image = (isset($item['submenu_bg_image']) && $item['submenu_bg_image'])
                ? $item['submenu_bg_image']
                : '';
            $background_repeat = $item['background_repeat']
                ? 'background-repeat: '.$item['background_repeat'].' !important;'
                : '';
            $background_position = $item['background_position']
                ? 'background-position: '.$item['background_position'].' !important;'
                : '';
            $background_size = $item['background_size']
                ? 'background-size: '.$item['background_size'].' !important;'
                : '';
            $title = (isset($item['title']) && $item['title'])
                ? $item['title']
                : '';
            $item_class = (isset($item['classes']) && $item['classes'])
                ? ' '.$item['classes'].''
                : '';
            $lable = (isset($item['lable']) && $item['lable'])
                ? $item['lable']
                :'';
            $full_width_multicolunm = (isset($item['full_width_multicolunm']) && $item['full_width_multicolunm'])
                ? $item['full_width_multicolunm']
                : '';
            $full_width_block_content = (isset($item['full_width_block_content']) && $item['full_width_block_content'])
                ? $item['full_width_block_content']
                : '';
            $dynamic_content_mul = (isset($item['dynamic_content_mul']) && $item['dynamic_content_mul'])
                ? $item['dynamic_content_mul']
                : '';
            $dynamic_block_content = (isset($item['dynamic_block_content']) && $item['dynamic_block_content'])
                ? $item['dynamic_block_content']
                : '';
            $menu_block_content = (isset($item['block_content']) && $item['block_content'])
                ? $item['block_content']
                : '';
            $menu_top_content = (isset($item['block_top']) && $item['block_top'])
                ? $item['block_top']:'';
            $menu_left_content = (isset($item['block_left']) && $item['block_left'])
                ? $item['block_left']
                : '';
            $menu_left_width = (isset($item['block_left_width']) && $item['block_left_width'])
                ? $item['block_left_width']
                : 0;
            $menu_right_content = (isset($item['block_right']) && $item['block_right'])
                ? $item['block_right']
                : '';
            $menu_right_width = (isset($item['block_right_width']) && $item['block_right_width'])
                ? $item['block_right_width']
                : 0;
            $menu_bottom_content = (isset($item['block_bottom']) && $item['block_bottom'])
                ? $item['block_bottom']
                : '';
            $submenu_type = (isset($item['submenu_type']) && $item['submenu_type'])
                ? $item['submenu_type']
                : '';
            $menu_columns = (isset($item['submenu_columns']) && $item['submenu_columns'])
                ? $item['submenu_columns']
                : 4;
            $type_menu = (isset($item['type_menu']) && $item['type_menu'])
                ? $item['type_menu']
                : '';
            if ($type_menu == 'cmspage') {
                $item_menu = $this->loadCMSPage($item['menu_object_id']);
                $url = $this->storeManager->getStore()->getUrl($item_menu->getIdentifier());
            } elseif ($type_menu == 'category') {
                $item_menu = $this->getCategoryModel($item['menu_object_id']);
                if ($item_menu && !$item_menu->getIsActive()) {
                    continue;
                }
                if ($item_menu && !$item_menu->getIncludeInMenu()) {
                    continue;
                }
                $url = $this->_categoryHelper->getCategoryUrl($item_menu);
            } else {
                $url = (isset($item['url']) && $item['url'])
                    ? $this->getMenuCustomLink($item['url'])
                    : '#';
            }
            
            // get children menu item.
            $children = $this->getResourceMenuBuilderItem()->getChildrenItems($menu_id, $item['entity_id']);
            $item_class .= ' item-'.$item['entity_id'].'';
            $item_class .= ($children && $submenu_type == 'multicolumn_dropdown')
                ? $full_width_multicolunm?' fullwidth':' custom-static-width'
                : '';
            $item_class .= ($submenu_type == 'block_content')
                ? $full_width_block_content?' fullwidth':' custom-static-width'
                : '';
            if ($submenu_type == 'multicolumn_dropdown' && !$full_width_multicolunm && $dynamic_content_mul) {
                $cssInline .= $this->addCssDynamicContent(
                    $item['entity_id']
                );
                $item_class .= ' dynamic-content';
            } else if ($submenu_type == 'block_content' && !$full_width_block_content && $dynamic_block_content) {
                $cssInline .= $this->addCssDynamicContent(
                    $item['entity_id']
                );
                $item_class .= ' dynamic-content';
            }
            $submenu_class = ($submenu_type == 'multicolumn_dropdown')
                ? ' multicolumn submenu-'.$item['entity_id'].''
                : '';
            if ($children || $submenu_type == 'block_content') {
                $item_class .= ' menu-item-has-children';
            }
            $item_class .= ' '.$submenu_type.'';
            
            // add html menu item.
            $html .= '<li class="ui-menu-item level0'.$item_class.'">';
            
            $html .= '<a href="'.$url.'" class="level-top">';
            if ($item['icon_image']) {
                $html .= '<img class="menu-thumb-icon" 
                    src="' . $this->_categoryMenuHelper->getImageUrl($icon_image) . '" alt="'.$title.'"/>';
            } elseif ($item['font_icon']) {
                $html .= '<em class="menu-thumb-icon '.$item['font_icon'].'"></em>';
            }
            if ($children) {
                $html .= '<span>'.$title.'</span>';
            } else {
                $html .= $title;
            }
            if ($lable) {
                $html .= '<span class="label item-label-'.$item['entity_id'].' label-'.$lable.'">'.$lable.'</span>';
                $cssInline .= $this->addCssLable(
                    $item['entity_id'],
                    $lable
                );
            }
            $html .= '</a>';
            if ($children || ($submenu_type == 'block_content' && $menu_block_content)) {
                $html .= '<div class="open-children-toggle"></div>';
            }
            if ($children || ($submenu_type == 'block_content' && $menu_block_content)) {
                $html .= '<div class="submenu'.$submenu_class.'">';
                if ($menu_top_content && $submenu_type == 'multicolumn_dropdown') {
                    $html .= '<div class="menu-top-block">'.$this->getBlockContent($menu_top_content).'</div>';
                }
                if ($submenu_type == 'block_content' && $menu_block_content) {
                    $html .= '<div class="submenu-block-content">';
                    $html .= '<div class="menu-block-content">'.$this->getBlockContent($menu_block_content).'</div>';
                }
                if ($children) {
                    if ($submenu_type == 'multicolumn_dropdown') {
                        $html .= '<div class="row">';
                        $cssInline .= $this->addCssMulticolumn(
                            $item['entity_id'],
                            $submenu_bg_image,
                            $background_repeat,
                            $background_position,
                            $background_size
                        );
                    }
                    if ($menu_left_content && $menu_left_width > 0 && $submenu_type == 'multicolumn_dropdown') {
                        $html .= '<div class="menu-left-block col-sm-'.$menu_left_width.'">
                            '.$this->getBlockContent($menu_left_content).'</div>';
                    }
                    $html .= $this->getSubmenuItemsHtml(
                        $children,
                        $menu_id,
                        1,
                        $max_level,
                        12-$menu_left_width-$menu_right_width,
                        $submenu_type,
                        $menu_columns
                    );
                    if ($menu_right_content && $menu_right_width > 0 && $submenu_type == 'multicolumn_dropdown') {
                        $html .= '<div class="menu-right-block col-sm-'.$menu_right_width.'">
                            '.$this->getBlockContent($menu_right_content).'</div>';
                    }
                    if ($submenu_type == 'multicolumn_dropdown') {
                        $html .= '</div>';
                    }
                }
                if ($submenu_type == 'block_content' && $menu_block_content) {
                    $html .= '</div>';
                }
                if ($menu_bottom_content && $submenu_type == 'multicolumn_dropdown') {
                    $html .= '<div class="menu-bottom-block">'.$this->getBlockContent($menu_bottom_content).'</div>';
                }
                $html .= '</div>';
            }
            if ($cssInline) {
                $html .= $this->getInlineCss($cssInline);
            }
            $html .= '</li>';
        }
        return $html;
    }
    
    /**
     * return html sub menu
     */
    public function getSubmenuItemsHtml(
        $children,
        $menu_id,
        $level = 1,
        $max_level = 0,
        $column_width = 12,
        $submenu_type = 'multicolumn_dropdown',
        $columns = null
    ) {
        $html = '';
        if (!$max_level ||
            ($max_level && $max_level == 0) ||
            ($max_level && $max_level > 0 && $max_level-1 >= $level)) {
            $column_class = "";
            if ($level == 1 && $columns && $submenu_type == 'multicolumn_dropdown') {
                $column_class = " col-sm-".$column_width."";
                $column_class .= " columns".$columns;
            }
            $html = '<ul class="subchildmenu'.$column_class.'">';
            foreach ($children as $child) {
                
                $cssInline = '';
                // get config menu item.
                $icon_image = (isset($child['classes']) && $child['icon_image'])
                    ? $child['icon_image']
                    : '';
                $title = (isset($child['title']) && $child['title'])
                    ? $child['title']
                    : '';
                $item_class = (isset($child['classes']) && $child['classes'])
                    ? ' '.$child['classes'].''
                    : '';
                $lable = (isset($child['lable']) && $child['lable'])
                    ? $child['lable']
                    : '';
                $lable_color = (isset($child['lable_color']) && $child['lable_color'])
                    ? $child['lable_color']
                    : '';
                $menu_block_content = (isset($child['block_content']) && $child['block_content'])
                    ? $child['block_content']
                    : '';
                $child_menu_type = (isset($child['submenu_type']) && $child['submenu_type'])
                    ? $child['submenu_type']
                    : '';
                $type_menu = (isset($child['type_menu']) && $child['type_menu'])
                    ? $child['type_menu']
                    : '';
                if ($type_menu == 'cmspage') {
                    $item_menu = $this->loadCMSPage($child['menu_object_id']);
                    $url = $this->storeManager->getStore()->getUrl($item_menu->getIdentifier());
                } elseif ($type_menu == 'category') {
                    $item_menu = $this->getCategoryModel($child['menu_object_id']);
                    if (!$item_menu->getData('is_active')) {
                        continue;
                    }
                    if (!$item_menu->getData('include_in_menu')) {
                        continue;
                    }
                    $url = $this->_categoryHelper->getCategoryUrl($item_menu);
                } else {
                    $url = (isset($child['url']) && $child['url'])
                        ? $this->getMenuCustomLink($child['url'])
                        : '#';
                }
                $item_class .= ' level'.$level.'';
                $item_class .= ' item-'.$child['entity_id'].'';
                
                // get children menu item.
                $sub_children = $this->getResourceMenuBuilderItem()->getChildrenItems($menu_id, $child['entity_id']);
                
                if ($sub_children) {
                    $item_class .= ' menu-item-has-children';
                }
                // add html menu item.
                $html .= '<li class="ui-menu-item'.$item_class.'">';
                $html .= '<a href="'.$url.'">';
                if ($child['icon_image']) {
                    $html .= '<img class="menu-thumb-icon" 
                        src="' . $this->_categoryMenuHelper->getImageUrl($icon_image) . '" alt="'.$title.'"/>';
                }
                if ($sub_children) {
                    $html .= '<span>'.$title.'</span>';
                } else {
                    $html .= $title;
                }
                if ($lable) {
                    $html .= '<span class="label item-label-'.$child['entity_id'].' 
                        label-'.$lable.'">'.$lable.'</span>';
                    $cssInline .= $this->addCssLable(
                        $child['entity_id'],
                        $lable
                    );
                }
                $html .= '</a>';
                if ($sub_children) {
                    $html .= '<div class="open-children-toggle"></div>';
                }
                if ($sub_children || ($child_menu_type == 'block_content' && $menu_block_content)) {
                    if ($child_menu_type == 'block_content' && $menu_block_content) {
                        $html .= '<div class="submenu-block-content">';
                        $html .= '<div class="menu-block-content">
                            '.$this->getBlockContent($menu_block_content).'</div>';
                    }
                    if ($sub_children) {
                        $html .= $this->getSubmenuItemsHtml(
                            $sub_children,
                            $menu_id,
                            $level+1,
                            $max_level,
                            $column_width,
                            $submenu_type
                        );
                    }
                    if ($child_menu_type == 'block_content' && $menu_block_content) {
                        $html .= '</div>';
                    }
                }
                if ($cssInline) {
                    $html .= $this->getInlineCss($cssInline);
                }
                $html .= '</li>';
            }
            $html .= '</ul>';
        }
        return $html;
    }
    
    /**
     * return data config Admin
     */
    public function getData($config)
    {
        $config = $this->helper->getData($config);
        return $config;
    }
    
    /**
     * Returns inline css to menu
     *
     * @param string $cssSetupObject
     * @return string
     */
    public function addCssMulticolumn(
        $item_id,
        $submenu_bg_image,
        $background_repeat,
        $background_position,
        $background_size
    ) {
        $cssInline = '';
        if ($submenu_bg_image) {
            $background = $this->_categoryMenuHelper->getImageBackgroundUrl($submenu_bg_image);
            $cssInline .= '.ui-menu-item .submenu-'.$item_id.'{background-image: url("' . $background . '") !important;
                '.$background_repeat.''.$background_position.''.$background_size.'}';
        }
        return $cssInline;
    }
    
    /**
     * Returns inline css to menu
     *
     * @param string $cssSetupObject
     * @return string
     */
    public function addCssLable(
        $item_id,
        $lable
    ) {
        $lable_bg = $this->getData('menus/builder_label/'.$lable.'_bg_label');
        $lable_text = $this->getData('menus/builder_label/'.$lable.'_text_label');
        $cssInline = '';
        if ($lable_bg) {
            $lable_bg = (strlen($lable_bg) > 6)?$lable_bg:'#'.$lable_bg;
            $cssInline .= '.ui-menu-item .item-label-'.$item_id.'{background-color: '.$lable_bg.';}
                .ui-menu-item .item-label-'.$item_id.':before{border-color: '.$lable_bg.';}';
        }
        if ($lable_text) {
            $lable_text = (strlen($lable_text) > 6)?$lable_text:'#'.$lable_text;
            $cssInline .= '.ui-menu-item .item-label-'.$item_id.'{color: '.$lable_text.';}';
        }
        return $cssInline;
    }

    /**
     * Returns inline css to menu
     *
     * @param string $cssSetupObject
     * @return string
     */
    public function addCssDynamicContent(
        $item_id
    ) {
        $cssInline = '.ui-menu-item.item-'.$item_id.' .submenu{width: 1200px;}';
        return $cssInline;
    }
    
    /**
     * Returns inline css to menu
     *
     * @param string $cssSetupObject
     * @return string
     */
    public function getInlineCss($cssSetupObject)
    {
        return $this->secureRenderer->renderTag('style', [], $cssSetupObject, false);
    }

    /**
     * Returns menu custom link
     *
     * @param string $cssSetupObject
     * @return string
     */
    public function getMenuCustomLink($url)
    {
        $regularExpression = '/{{([a-z]{0,10})(.*?)}}(?:(.*?)(?:{{\/(?:\\1)}}))?/si';
        if (preg_match_all($regularExpression, $url, $constructions, PREG_SET_ORDER)) {
            foreach ($constructions as $construction) {
                if (isset($construction[1]) && isset($construction[2]) && !isset($construction[3])) {
                    $validate_url = false;
                    $code_url = $construction[1];
                    if ($code_url == 'store' || $code_url == 'base') {
                        $validate_url = $code_url;
                    } else {
                        $string = trim($construction[2]);
                        $array_string = explode(" ",$string);
                        if(isset($array_string[0]) && $array_string[0]){
                            $code_url = str_replace(' ', '', $array_string[0]);
                            if ($code_url == 'store' || $code_url == 'base') {
                                $validate_url = $code_url;
                            }
                        }
                    }
                    if (preg_match_all("/url='([^>]*?)(.*?)'/is", $construction[2], $data_urls, PREG_SET_ORDER) 
                        && $validate_url) {
                        foreach ($data_urls as $data_url) {
                            if(isset($data_url[2])){
                                if ($validate_url == 'store') {
                                    $replacedValue = $this->storeManager->getStore()->getUrl($data_url[2]);
                                } else {
                                    $replacedValue = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB).$data_url[2];
                                }
                                $value = str_replace($construction[0], $replacedValue, $url);
                                return $value;
                            }
                        }
                    }
                    if (preg_match_all('/url="([^>]*?)(.*?)"/is', $construction[2], $data_urls, PREG_SET_ORDER) 
                        && $validate_url) {
                        foreach ($data_urls as $data_url) {
                            if(isset($data_url[2])){
                                if ($validate_url == 'store') {
                                    $replacedValue = $this->storeManager->getStore()->getUrl($data_url[2]);
                                } else {
                                    $replacedValue = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB).$data_url[2];
                                }
                                $value = str_replace($construction[0], $replacedValue, $url);
                                return $value;
                            }
                        }
                    }
                }
            }
        }
        return $url;
    }
}
