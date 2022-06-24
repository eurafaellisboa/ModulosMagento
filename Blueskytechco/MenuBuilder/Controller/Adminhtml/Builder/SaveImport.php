<?php
/**
 * Copyright © 2021 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Blueskytechco\MenuBuilder\Controller\Adminhtml\Builder;

use Blueskytechco\MenuBuilder\Controller\Adminhtml\AbstractMenu;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Menu Builder Save Import
 *
 */
class SaveImport extends AbstractMenu
{
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if (isset($_FILES['data']) && ($_FILES['data']['error'] == UPLOAD_ERR_OK)) {
            if (isset($_FILES['data']['type']) && $_FILES['data']['type'] !== 'text/xml') {
                $message = __('Incorrect upload file format.');
                $this->messageManager->addErrorMessage($message);
                return $resultRedirect->setPath('*/*/import');
            }
            $xml = file_get_contents($_FILES['data']['tmp_name']);  
            $importPath = BP. '/' . DirectoryList::PUB . '/' . DirectoryList::MEDIA . '/menu_importer/';
            $fileName = $importPath.'menu_builder_'.strtotime(date("Y-m-d H:i:s")).'.xml';
            file_put_contents($fileName, $xml);
            $data_file_import = $this->_parser->load($fileName)->xmlToArray();

            if (isset($data_file_import['root']['menus'])) {
                $array_child_item = [];
                $menus_data = $data_file_import['root']['menus'];
                $menu_name = (isset($menus_data['name']) && $menus_data['name'])
                    ? $menus_data['name']
                    : __('Menu Name');
                $menu_type = (isset($menus_data['type']) && $menus_data['type'])
                    ? $menus_data['type']
                    : 1;
                $menus = $this->menuBuilderFactory->create();
                $menus->setIdentifier(time().rand());
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
                $this->messageManager->addSuccessMessage(__('Import Menus Success.'));
                return $resultRedirect->setPath('*/*/edit', ['id' => $menus_id, '_current' => true]);
            }
        }
        $message = __('Import failed.');
        $this->messageManager->addErrorMessage($message);
        return $resultRedirect->setPath('menus/builder');
    }
}
