<?php
namespace Blueskytechco\MenuBuilder\Helper;

use Magento\Framework\Registry;
use Magento\Store\Model\ScopeInterface;
use Blueskytechco\MenuBuilder\Model\MenuBuilderItemMetaFactory;
use Magento\Framework\View\Helper\SecureHtmlRenderer;

class Admin extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var \Blueskytechco\MenuBuilder\Model\MenuBuilderItemMetaFactory
     */
    protected $menuBuilderItemMetaFactory;
    
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    
    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $wysiwygConfig;
    
    /**
     * @var SecureHtmlRenderer
     */
    protected $secureRenderer;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        MenuBuilderItemMetaFactory $menuBuilderItemMetaFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        SecureHtmlRenderer $secureRenderer,
        Registry $registry,
        \Magento\Backend\Model\Url $backendUrlManager
    ) {
        $this->_resource = $resource;
        $this->_registry = $registry;
        $this->wysiwygConfig = $wysiwygConfig;
        $this->_storeManager = $storeManager;
        $this->secureRenderer = $secureRenderer;
        $this->menuBuilderItemMetaFactory = $menuBuilderItemMetaFactory;
        $this->backendUrlManager  = $backendUrlManager;
        parent::__construct($context);
    }
    
    /**
     * Return BaseUrl
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }
    
    /**
     * Return Json Config Wysiwyg
     *
     * @return string
     */
    public function getJsonConfigWysiwyg()
    {
        $config = $this->wysiwygConfig->getConfig();
        $data = $config->getData();
        if (isset($data['plugins'][2]['name']) && $data['plugins'][2]['name'] == 'magentovariable') {
            unset($data['plugins'][2]);
        }
        if (isset($data['plugins'][1]['name']) && $data['plugins'][1]['name'] == 'magentowidget') {
            $data['plugins'][1]['options']['window_url'] = $this->backendUrlManager->getUrl('menus/widget/index');
        }
        $data['height'] = '100px';
        return $data;
    }
    
    /**
     * Return Menu Block Width
     *
     * @return array
     */
    public function getMenuBlockWidth()
    {
        $menu_block_width  = [
            '0' => ''.__('Do not show').'',
            '1' => '1/12',
            '2' => '2/12',
            '3' => '3/12',
            '4' => '4/12',
            '5' => '5/12',
            '6' => '6/12',
            '7' => '7/12',
            '8' => '8/12',
            '9' => '9/12',
            '10' => '10/12',
            '11' => '11/12',
            '12' => '12/12',
        ];
        return $menu_block_width;
    }
    
    /**
     * Return Editor top Buttons HTML
     *
     * @return string
     */
    public function _getButtonsHtml($id)
    {
        $buttonsHtml = '<div id="buttons' . $id . '" class="buttons-set">';
        $buttonsHtml .= $this->_getToggleButtonHtml($id);
        $buttonsHtml .= '</div>';

        return $buttonsHtml;
    }
    
    /**
     * Return HTML button to toggling WYSIWYG
     *
     * @param bool $visible
     * @return string
     */
    public function _getToggleButtonHtml($id)
    {
        $html = $this->_getButtonHtml(
            [
                'title' => __('Show / Hide Editor'),
                'class' => 'action-show-hide',
                'style' => '',
                'id' => 'toggle' . $id,
            ]
        );
        return $html;
    }
    
    /**
     * Return custom button HTML
     *
     * @param array $data Button params
     * @return string
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function _getButtonHtml($data)
    {
        $id = empty($data['id'])
            ? 'buttonId' .$this->random->getRandomString(10)
            : $data['id'];

        $html = '<button type="button"';
        $html .= ' class="scalable ' . (isset($data['class']) ? $data['class'] : '') . '"';
        $html .= ' id="' . $id . '"';
        $html .= '>';
        $html .= isset($data['title']) ? '<span><span><span>' . $data['title'] . '</span></span></span>' : '';
        $html .= '</button>';
        if (!empty($data['onclick'])) {
            $html .= $this->secureRenderer->renderEventListenerAsTag('onclick', $data['onclick'], "#$id");
        }
        if (!empty($data['style'])) {
            $html .= $this->secureRenderer->renderStyleAsTag($data['style'], "#$id");
        }

        return $html;
    }
    
    /**
     * Return Config Lable
     *
     * @return array
     */
    public function getDataLable()
    {
        $lables  = [
            'new' => ''.__('New').'',
            'sale' => ''.__('Sale').'',
            'hot' => ''.__('Hot').'',
        ];
        return $lables;
    }
    
    /**
     * Return Config Sub Menu Type
     *
     * @return array
     */
    public function getSubmenuType()
    {
        $submenu_types  = [
            'default_dropdown' => ''.__('Standard Submenu').'',
            'multicolumn_dropdown' => ''.__('Multicolumn Submenu').'',
            'block_content' => ''.__('Block Content').'',
        ];
        return $submenu_types;
    }
    
    /**
     * Return Background Repeat
     *
     * @return array
     */
    public function getBackgroundRepeat()
    {
        $background_repeat  = [
            'repeat' => ''.__('Repeat').'',
            'no-repeat' => ''.__('No Repeat').'',
            'repeat-x' => ''.__('Repeat X').'',
            'repeat-y' => ''.__('Repeat Y').'',
        ];
        return $background_repeat;
    }
    
    /**
     * Return Background Position
     *
     * @return array
     */
    public function getBackgroundPosition()
    {
        $background_position  = [
            'center' => ''.__('Center').'',
            'center left' => ''.__('Center Left').'',
            'center right' => ''.__('Center Right').'',
            'top left' => ''.__('Top Left').'',
            'top center' => ''.__('Top Center').'',
            'top right' => ''.__('Top Right').'',
            'bottom left' => ''.__('Bottom Left').'',
            'bottom center' => ''.__('Bottom Center').'',
            'bottom right' => ''.__('Bottom Right').'',
        ];
        return $background_position;
    }
    
    /**
     * Return Background Size
     *
     * @return array
     */
    public function getBackgroundSize()
    {
        $background_size  = [
            'auto' => ''.__('Keep original').'',
            '100% auto' => ''.__('Stretch to width').'',
            'auto 100%' => ''.__('Stretch to height').'',
            'cover' => ''.__('Cover').'',
            'contain' => ''.__('Contain').'',
        ];
        return $background_size;
    }
    
    /**
     * Return Menu Item Html
     *
     * @return string
     */
    public function getMenuItemSettingsHtml(
        $title,
        $url,
        $type_menu,
        $menu_item_id,
        $menu_id,
        $depth,
        $parent_item
    ) {
        $lables = $this->getDataLable();
        $submenu_types = $this->getSubmenuType();
        $background_repeats = $this->getBackgroundRepeat();
        $background_positions = $this->getBackgroundPosition();
        $background_sizes = $this->getBackgroundSize();
        $menu_block_width = $this->getMenuBlockWidth();
        $data_item_meta = $this->menuBuilderItemMetaFactory->create()->getResource()->getDataItemMeta($menu_item_id);
        $parent_id = (isset($data_item_meta['parent_id']) && $data_item_meta['parent_id'])
            ? $data_item_meta['parent_id']
            : $parent_item;
        $data_db_id = (isset($data_item_meta['data_db_id']) && $data_item_meta['data_db_id'])
            ? $data_item_meta['data_db_id']
            : $menu_item_id;
        $block_right = (isset($data_item_meta['block_right']) && $data_item_meta['block_right'])
            ? $data_item_meta['block_right'] : '';
        $block_left = (isset($data_item_meta['block_left']) && $data_item_meta['block_left'])
            ? $data_item_meta['block_left'] : '';
        $block_right_width = (isset($data_item_meta['block_right_width']) && $data_item_meta['block_right_width'])
            ? $data_item_meta['block_right_width'] : 0;
        $block_left_width = (isset($data_item_meta['block_left_width']) && $data_item_meta['block_left_width'])
            ? $data_item_meta['block_left_width'] : 0;
        $title = (isset($data_item_meta['title']) && $data_item_meta['title'])
            ? $data_item_meta['title'] : $title;
        $classes = (isset($data_item_meta['classes']) && $data_item_meta['classes'])
            ? $data_item_meta['classes'] : '';
        $lable_item = (isset($data_item_meta['lable']) && $data_item_meta['lable'])
            ? $data_item_meta['lable'] : '';
        $font_icon = (isset($data_item_meta['font_icon']) && $data_item_meta['font_icon'])
            ? $data_item_meta['font_icon'] : '';
        $lable_color = (isset($data_item_meta['lable_color']) && $data_item_meta['lable_color'])
            ? $data_item_meta['lable_color'] : '';
        $submenu_type = (isset($data_item_meta['submenu_type']) && $data_item_meta['submenu_type'])
            ? $data_item_meta['submenu_type'] : '';
        $submenu_columns = (isset($data_item_meta['submenu_columns']) && $data_item_meta['submenu_columns'])
            ? $data_item_meta['submenu_columns'] : '';
        $submenu_bg_image = (isset($data_item_meta['submenu_bg_image']) && $data_item_meta['submenu_bg_image'])
            ? $data_item_meta['submenu_bg_image'] : '';
        $background_repeat = (isset($data_item_meta['background_repeat']) && $data_item_meta['background_repeat'])
            ? $data_item_meta['background_repeat'] : '';
        $background_position = (isset($data_item_meta['background_position']) && $data_item_meta['background_position'])
            ? $data_item_meta['background_position'] : '';
        $background_size = (isset($data_item_meta['background_size']) && $data_item_meta['background_size'])
            ? $data_item_meta['background_size'] : '';
        $block_content = (isset($data_item_meta['block_content']) && $data_item_meta['block_content'])
            ? $data_item_meta['block_content'] : '';
        $block_top = (isset($data_item_meta['block_top']) && $data_item_meta['block_top'])
            ? $data_item_meta['block_top'] : '';
        $block_bottom = (isset($data_item_meta['block_bottom']) && $data_item_meta['block_bottom'])
            ? $data_item_meta['block_bottom'] : '';
        $icon_image = (isset($data_item_meta['icon_image']) && $data_item_meta['icon_image'])
            ? $data_item_meta['icon_image'] : '';
        $full_width_multicolunm = (isset($data_item_meta['full_width_multicolunm']) && $data_item_meta['full_width_multicolunm'])
            ? $data_item_meta['full_width_multicolunm'] : '';
        $dynamic_content_mul = (isset($data_item_meta['dynamic_content_mul']) && $data_item_meta['dynamic_content_mul'])
            ? $data_item_meta['dynamic_content_mul'] : '';
        $full_width_block_content = (isset($data_item_meta['full_width_block_content']) && $data_item_meta['full_width_block_content'])
            ? $data_item_meta['full_width_block_content'] : '';
        $dynamic_block_content = (isset($data_item_meta['dynamic_block_content']) && $data_item_meta['dynamic_block_content'])
            ? $data_item_meta['dynamic_block_content'] : '';
        $url = (isset($data_item_meta['url']) && $data_item_meta['url'])
            ? $data_item_meta['url'] : $url;
        
        $html = '';
        $html .= '<div class="menu-item-settings" id="menu-item-settings-'.$menu_item_id.'" style="display:none">';
            $html .= '<div class="description description-thin">';
                $html .= '<label for="edit-menu-item-title-'.$menu_item_id.'">'.__('Title').'</label>';
                $html .= '<div class="field-menu-item">';
                    $html .= '<input type="text" data-form-part="menus_builder_form" 
                        id="edit-menu-item-title-'.$menu_item_id.'" class="edit-menu-item-title" 
                        name="menu-item['.$menu_item_id.'][title]" value="'.$title.'">';
                $html .= '</div>';
            $html .= '</div>';
        if ($type_menu == 'customlink') {
            $html .= '<div class="description description-thin">';
                $html .= '<label for="edit-menu-item-url-'.$menu_item_id.'">'.__('URL').'</label>';
                $html .= '<div class="field-menu-item">';
                    $html .= '<input type="text" data-form-part="menus_builder_form" 
                        id="edit-menu-item-url-'.$menu_item_id.'" class="edit-menu-item-url" 
                        name="menu-item['.$menu_item_id.'][url]" value="'.htmlentities($url, ENT_QUOTES).'">';
                $html .= '</div>';
            $html .= '</div>';
        }
            $html .= '<div class="field-css-classes description description-thin">';
                $html .= '<label for="edit-menu-item-classes-'.$menu_item_id.'">'.__('Add Class').'</label>';
                $html .= '<div class="field-menu-item">';
                    $html .= '<input type="text" id="edit-menu-item-classes-'.$menu_item_id.'" 
                        data-form-part="menus_builder_form" class="edit-menu-item-classes" 
                        name="menu-item['.$menu_item_id.'][classes]" value="'.$classes.'">';
                $html .= '</div>';
            $html .= '</div>';
        if (count($lables) > 0) {
            $html .= '<div class="field-css-lable description description-thin">';
                $html .= '<label for="edit-menu-item-lable-'.$menu_item_id.'">'.__('Add Label').'</label>';
                $html .= '<div class="field-menu-item">';
                    $html .= '<select id="edit-menu-item-lable-'.$menu_item_id.'" 
                        data-form-part="menus_builder_form" class="edit-menu-item-lable" 
                        name="menu-item['.$menu_item_id.'][lable]">';
                        $html .= '<option value="">'.__('Select Label').'</option>';
            foreach ($lables as $key => $lable) {
                $selected = ($lable_item == $key) ? 'selected' : '';
                $html .= '<option value="'.$key.'" '.$selected.'>'.ucwords($lable).'</option>';
            }
                    $html .= '</select>';
                $html .= '</div>';
            $html .= '</div>';
        }
            $html .= '<div id="menu-item-image-icon'.$menu_item_id.'" class="menu-item-image-icon'.$menu_item_id.'">';
                $html .= '<div class="admin_field">';
                    $html .= '<label>';
                        $html .= ''.__('Icon Image').'';
                    $html .= '</label>';
                $html .= '</div>';
                $html .= '<div class="option_field">';
                    $html .= '<div class="image-icon-upload-container">';
                        $html .= '<input name="icon_image'.$menu_item_id.'" class="menu_item_icon_image" type="file" 
                            data-url="'.$this->backendUrlManager->getUrl('menus/builder_uploader/iconimage', []).'">';
                        $hide_image_icon = ($icon_image == '')?'style="display:none"':'';
                        $html .= '<div class="saved_image_icon" '.$hide_image_icon.'>';
                            $html .= '<input type="hidden" name="menu-item['.$menu_item_id.'][icon_image]" 
                                data-form-part="menus_builder_form" class="icon_image_save" value="'.$icon_image.'">';
                            $html .= '<div class="saved_image_icon_html">';
                                $src_icon = $icon_image?$this->getBaseUrl().'menus/submenu/icon/' . $icon_image:'';
                                $html .= '<img class="saved_icon_image" src="' . $src_icon . '"><br> ';
                                $html .= '<button class="deleteImageIcon">'.__('Delete').'</button>';
                            $html .= '</div>';
                        $html .= '</div>';
                    $html .= '</div>';
                $html .= '</div>';
            $html .= '</div>';
            $html .= '<div id="menu-item-font-icon'.$menu_item_id.'" class="menu-item-font-icon'.$menu_item_id.'">';
                $html .= '<div class="admin_field">';
                    $html .= '<label>';
                        $html .= ''.__('Icon Font Class').'';
                    $html .= '</label>';
                $html .= '</div>';
                $html .= '<div class="field-menu-item">';
                    $html .= '<input type="text" id="edit-menu-item-font-'.$menu_item_id.'" 
                        data-form-part="menus_builder_form" class="edit-menu-item-font-icon" 
                        name="menu-item['.$menu_item_id.'][font_icon]" value="'.$font_icon.'">';
                $html .= '</div>';
            $html .= '</div>';
            $html .= '<div id="menu-item-submenu_type'.$menu_item_id.'" 
                class="option row menu-item-submenu_type'.$menu_item_id.' select_type">';
                $html .= '<div class="admin_field_type">';
                    $html .= '<div class="caption">';
                        $html .= ''.__('Options of Dropdown').'';
                    $html .= '</div>';
                    $html .= '<div class="descr">';
                        $html .= '<label>'.__('Submenu Type').'</label>';
                        $html .= '<div class="option_field menu-field">';
                            $html .= '<select class="form-control input-sm menu-item-submenu_type" 
                                data-form-part="menus_builder_form" name="menu-item['.$menu_item_id.'][submenu_type]">';
        foreach ($submenu_types as $key => $type) {
            $hidden = ($depth !== 0 && $key == 'multicolumn_dropdown')?'hidden':'';
            $selected = ($submenu_type == $key && !$hidden) ? 'selected' : '';
            $html .= '<option value="'.$key.'" '.$selected.' '.$hidden.'>'.$type.'</option>';
        }
                            $html .= '</select>';
                        $html .= '</div>';
                    $html .= '</div>';
                    $hide_multicolumn_dropdown = ($submenu_type !== 'multicolumn_dropdown' || $depth !== 0)
                        ? 'style="display:none"':'';
                    $html .= '<div class="multicolumn_dropdown_field" '.$hide_multicolumn_dropdown.'>';
                        $html .= '<div id="menu-item-submenu_columns'.$menu_item_id.'"  
                            class="menu-field option row menu-item-submenu_columns'.$menu_item_id.' select_type">';
                            $html .= '<div class="admin_field">';
                                $html .= '<label>';
                                    $html .= ''.__('Submenu Columns').'';
                                $html .= '</label>';
                            $html .= '</div>';
                            $html .= '<div class="option_field">';
                                $html .= '<select class="form-control input-sm" data-form-part="menus_builder_form" 
                                    name="menu-item['.$menu_item_id.'][submenu_columns]">';
        for ($i = 1; $i <= 10; $i++) {
            $selected = ($submenu_columns == $i) ? 'selected' : '';
            $html .= '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
        }
                                $html .= '</select>';
                            $html .= '</div>';
                        $html .= '</div>';
                        $checkbox_full_width_multicolunm = $full_width_multicolunm?' checked':'';
                        $html .= '<div class="submenu_full_width menu-field ">';
                            $html .= '<td class="use-default">';
                            $html .= '<input id="item_full_width_'.$menu_item_id.'" data-form-part="menus_builder_form"
                                name="menu-item['.$menu_item_id.'][full_width_multicolunm]" type="checkbox" 
                                class="checkbox"'.$checkbox_full_width_multicolunm.'>';
                                $html .= '<label for="item_full_width_'.$menu_item_id.'" >'.__('Full Width').'</label>';
                            $html .= '</td>';
                        $html .= '</div>';
                        $dynamic_content_mul = $dynamic_content_mul?' checked':'';
                        $html .= '<div class="submenu_dynamic_content menu-field ">';
                            $html .= '<td class="use-default">';
                            $html .= '<input id="item_dynamic_content_'.$menu_item_id.'" data-form-part="menus_builder_form"
                                name="menu-item['.$menu_item_id.'][dynamic_content_mul]" type="checkbox" 
                                class="checkbox"'.$dynamic_content_mul.'>';
                                $html .= '<label for="item_dynamic_content_'.$menu_item_id.'" >'.__('Dynamic Content').'</label>';
                            $html .= '</td>';
                        $html .= '</div>';
                        $html .= '<div id="menu-item-submenu_background'.$menu_item_id.'" 
                            class="menu-item-submenu_background'.$menu_item_id.' select_type">';
                            $html .= '<div class="admin_field">';
                                $html .= '<label>';
                                    $html .= ''.__('Background Image').'';
                                $html .= '</label>';
                            $html .= '</div>';
                            $html .= '<div class="option_field">';
                                $html .= '<div class="image-upload-container">';
                                    $html .= '<input name="submenu_bg_image'.$menu_item_id.'" 
                                        class="submenu_bg_image" type="file" 
                                        data-url="
                                            '.$this->backendUrlManager->getUrl('menus/builder_uploader/bgimage').'
                                        ">';
                                    $hide_bg_image_html = ($submenu_bg_image == '')?'style="display:none"':'';
                                    $html .= '<div class="saved_image" '.$hide_bg_image_html.'>';
                                        $html .= '<input type="hidden" 
                                            name="menu-item['.$menu_item_id.'][submenu_bg_image]" 
                                            data-form-part="menus_builder_form" class="submenu_bg_image_save"
                                            value="'.$submenu_bg_image.'">';
                                        $html .= '<div class="saved_image_html">';
                                            $src_bg = $submenu_bg_image
                                                ? $this->getBaseUrl().'menus/submenu/background/' . $submenu_bg_image
                                                : '';
                                            $html .= '<img class="saved_image_img" src="' .$src_bg. '"><br> ';
                                            $html .= '<button class="deleteImage">'.__('Delete').'</button>';
                                        $html .= '</div>';
                                    $html .= '</div>';
                                $html .= '</div>';
                            $html .= '</div>';
                        $html .= '</div>';
                        $html .= '<div id="menu-item-submenu_background_repeat'.$menu_item_id.'" 
                            class="menu-item-background">';
                            $html .= '<select class="form-control input-sm" data-form-part="menus_builder_form" 
                                name="menu-item['.$menu_item_id.'][background_repeat]">';
        foreach ($background_repeats as $key => $type) {
            $selected = ($background_repeat == $key) ? 'selected' : '';
            $html .= '<option value="'.$key.'" '.$selected.'>'.$type.'</option>';
        }
                            $html .= '</select>';
                        $html .= '</div>';
                        $html .= '<div id="menu-item-submenu_background_position'.$menu_item_id.'" 
                            class="menu-item-background">';
                            $html .= '<select class="form-control input-sm" data-form-part="menus_builder_form" 
                                name="menu-item['.$menu_item_id.'][background_position]">';
        foreach ($background_positions as $key => $type) {
            $selected = ($background_position == $key) ? 'selected' : '';
            $html .= '<option value="'.$key.'" '.$selected.'>'.$type.'</option>';
        }
                            $html .= '</select>';
                        $html .= '</div>';
                        $html .= '<div id="menu-item-submenu_background_size'.$menu_item_id.'" 
                            class="menu-item-background size">';
                            $html .= '<select class="form-control input-sm" data-form-part="menus_builder_form" 
                                name="menu-item['.$menu_item_id.'][background_size]">';
        foreach ($background_sizes as $key => $type) {
            $selected = ($background_size == $key) ? 'selected' : '';
            $html .= '<option value="'.$key.'" '.$selected.'>'.$type.'</option>';
        }
                            $html .= '</select>';
                        $html .= '</div>';
                    $html .= '</div>';
                    $hide_block_content = ($submenu_type !== 'block_content')?'style="display:none"':'';
                    $html .= '<div class="block_type_content_field" '.$hide_block_content.'>';
                        $html .= '<div class="admin_field">';
                            $html .= '<div class="descr">';
                                $html .= '<label>'.__('Block Content').'</label>';
                                $html .= '<div class="option_field">';
                                    $html .= $this->_getButtonsHtml('block_content_'.$menu_item_id.'');
                                    $html .= '<textarea id="block_content_'.$menu_item_id.'" 
                                        data-block="block_content_'.$menu_item_id.'" 
                                        name="menu-item['.$menu_item_id.'][block_content]" 
                                        data-form-part="menus_builder_form" 
                                        class="input-text wysiwyg-input">'.$block_content.'</textarea>';
                                $html .= '</div>';
                            $html .= '</div>';
                        $html .= '</div>';
                        $checkbox_full_width_block_content = $full_width_block_content?' checked':'';
                        $html .= '<div class="submenu_full_width menu-field ">';
                            $html .= '<td class="use-default">';
                            $html .= '<input id="item_full_width_content_'.$menu_item_id.'" data-form-part="menus_builder_form"
                                name="menu-item['.$menu_item_id.'][full_width_block_content]" type="checkbox" 
                                class="checkbox"'.$checkbox_full_width_block_content.'>';
                                $html .= '<label for="item_full_width_content_'.$menu_item_id.'" >'.__('Full Width').'</label>';
                            $html .= '</td>';
                        $html .= '</div>';
                        $dynamic_block_content = $dynamic_block_content?' checked':'';
                        $html .= '<div class="submenu_dynamic_content menu-field ">';
                            $html .= '<td class="use-default">';
                            $html .= '<input id="item_dynamic_content_content_'.$menu_item_id.'" data-form-part="menus_builder_form"
                                name="menu-item['.$menu_item_id.'][dynamic_block_content]" type="checkbox" 
                                class="checkbox"'.$dynamic_block_content.'>';
                                $html .= '<label for="item_dynamic_content_content_'.$menu_item_id.'" >'.__('Dynamic Content').'</label>';
                            $html .= '</td>';
                        $html .= '</div>';
                    $html .= '</div>';
                $html .= '</div>';
            $html .= '</div>';
            $hide_block = ($depth !== 0)?'style="display:none"':'';
            $html .= '<div id="menu-item-block-'.$menu_item_id.'" 
                class="option row menu-item-block'.$menu_item_id.' add-block-content" '.$hide_block.'>';
                $html .= '<div class="caption add-block-html">';
                    $html .= ''.__('Add Block Content Html').'';
                $html .= '</div>';
                $html .= '<div class="block-content-html">';
                    $html .= '<div class="admin_field">';
                        $html .= '<div class="fieldset-title">
                            <p class="collapsible-title">'.__('Block Top').'</p></div>';
                        $html .= '<div class="descr" style="display:none">';
                            $html .= '<div class="option_field">';
                                $html .= $this->_getButtonsHtml('block_top_'.$menu_item_id.'');
                                $html .= '<textarea id="block_top_'.$menu_item_id.'" 
                                    data-block="block_top_'.$menu_item_id.'" 
                                    name="menu-item['.$menu_item_id.'][block_top]" 
                                    data-form-part="menus_builder_form" class="block-top input-text 
                                    wysiwyg-input">'.$block_top.'</textarea>';
                            $html .= '</div>';
                        $html .= '</div>';
                    $html .= '</div>';
                    $html .= '<div class="admin_field">';
                        $html .= '<div class="fieldset-title">
                            <p class="collapsible-title">'.__('Block Bottom').'</p></div>';
                        $html .= '<div class="descr" style="display:none">';
                            $html .= '<div class="option_field" >';
                                $html .= $this->_getButtonsHtml('block_bottom_'.$menu_item_id.'');
                                $html .= '<textarea id="block_bottom_'.$menu_item_id.'" 
                                    data-block="block_bottom_'.$menu_item_id.'" 
                                    name="menu-item['.$menu_item_id.'][block_bottom]" 
                                    data-form-part="menus_builder_form" 
                                    class="block-bottom input-text wysiwyg-input">'.$block_bottom.'</textarea>';
                            $html .= '</div>';
                        $html .= '</div>';
                    $html .= '</div>';
                    $html .= '<div class="admin_field">';
                        $html .= '<div class="fieldset-title">
                            <p class="collapsible-title">'.__('Block Left').'</p></div>';
                        $html .= '<div class="descr" style="display:none">';
                            $html .= '<div class="option_field">';
                                $html .= $this->_getButtonsHtml('block_left_'.$menu_item_id.'');
                                $html .= '<textarea id="block_left_'.$menu_item_id.'" 
                                    data-block="block_left_'.$menu_item_id.'" 
                                    name="menu-item['.$menu_item_id.'][block_left]" 
                                    data-form-part="menus_builder_form" class="block-left 
                                    input-text wysiwyg-input">'.$block_left.'</textarea>';
                            $html .= '</div>';
                            $html .= '<div class="option_field">';
                                $html .= '<div class="field_title"><p>'.__('Left Block Width').'</p></div>';
                                $html .= '<select class="form-control input-sm" 
                                    data-form-part="menus_builder_form" 
                                    name="menu-item['.$menu_item_id.'][block_left_width]">';
        foreach ($menu_block_width as $key => $width) {
            $selected = ($block_left_width == $key) ? 'selected' : '';
            $html .= '<option value="'.$key.'" '.$selected.'>'.$width.'</option>';
        }
                                $html .= '</select>';
                            $html .= '</div>';
                        $html .= '</div>';
                    $html .= '</div>';
                    $html .= '<div class="admin_field">';
                        $html .= '<div class="fieldset-title">
                            <p class="collapsible-title">'.__('Block Right').'</p></div>';
                        $html .= '<div class="descr" style="display:none">';
                            $html .= '<div class="option_field">';
                                $html .= $this->_getButtonsHtml('block_right_'.$menu_item_id.'');
                                $html .= '<textarea id="block_right_'.$menu_item_id.'" 
                                    data-block="block_right_'.$menu_item_id.'" 
                                    name="menu-item['.$menu_item_id.'][block_right]" 
                                    data-form-part="menus_builder_form" 
                                    class="block-right input-text wysiwyg-input">'.$block_right.'</textarea>';
                            $html .= '</div>';
                            $html .= '<div class="option_field">';
                                $html .= '<div class="field_title">
                                    <p>'.__('Right Block Width').'</p></div>';
                                $html .= '<select class="form-control input-sm" 
                                    data-form-part="menus_builder_form" 
                                    name="menu-item['.$menu_item_id.'][block_right_width]">';
        foreach ($menu_block_width as $key => $width) {
            $selected = ($block_right_width == $key) ? 'selected' : '';
            $html .= '<option value="'.$key.'" '.$selected.'>'.$width.'</option>';
        }
                                $html .= '</select>';
                            $html .= '</div>';
                        $html .= '</div>';
                    $html .= '</div>';
                $html .= '</div>';
            $html .= '</div>';
            $html .= '<div class="menu-item-actions description-wide submitbox">';
                $html .= '<a class="item-delete" data-id="'.$menu_item_id.'" href="">'.__('Remove').'</a>';
                $html .= '<a class="item-cancel" data-id="'.$menu_item_id.'" href="#">'.__('Cancel').'</a>';
            $html .= '</div>';
            $html .= '<input class="menu-item-data-db-id" type="hidden" 
                data-form-part="menus_builder_form" name="menu-item['.$menu_item_id.'][data_db_id]" 
                value="'.$data_db_id.'">';
            $html .= '<input class="menu-item-data-parent-id" type="hidden" 
                data-form-part="menus_builder_form" name="menu-item['.$menu_item_id.'][parent_id]" 
                value="'.$parent_id.'">';
        $html .= '</div>';
        return $html;
    }
}
