<?php
/**
 * Copyright © 2021 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Blueskytechco\MenuBuilder\Controller\Adminhtml\Builder;

use Blueskytechco\MenuBuilder\Controller\Adminhtml\AbstractMenu;
use Magento\Framework\Exception\LocalizedException;

/**
 * Menu Builder Save
 *
 */
class Save extends AbstractMenu
{
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();
        $menus = $this->menuBuilderFactory->create();
        $menu_item = $this->menuBuilderItemFactory->create();
        $resourceItemMeta = $this->menuBuilderItemMetaFactory->create()->getResource();
        $resourceItem = $menu_item->getResource();
        if ($data) {
            if (isset($data['entity_id']) && $data['entity_id']) {
                $entity_id = $data['entity_id'];
                try {
                    $menus = $menus->load($entity_id);
                    $menus->setName($data['menu-name']);
                    $menus->setType($data['menu-type']);
                    $menus->save();
                    $resourceItem->updateStatusItem($data['entity_id']);
                    if (isset($data['menu-item'])) {
                        $menu_items = $data['menu-item'];
                        $this->saveMenuItem($menu_items);
                    }
                    $resourceItem->getDeteteItem($data['entity_id']);
                    $this->messageManager->addSuccessMessage(__('The menus has been saved.'));
                    return $resultRedirect->setPath('*/*/edit', ['id' => $entity_id, '_current' => true]);
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                } catch (\RuntimeException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                } catch (\Exception $e) {
                    $message = __('Something went wrong while saving the menus.');
                    $this->messageManager->addExceptionMessage($e, $message);
                }
                return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
            } else {
                if (isset($data['menu-name']) && isset($data['menu-type'])
                    && $data['menu-name'] && $data['menu-type']) {
                    try {
                        $menus->setIdentifier(time().rand());
                        $menus->setName($data['menu-name']);
                        $menus->setType($data['menu-type']);
                        $menus->save();
                        $this->messageManager->addSuccessMessage(__('Create menus Success.'));
                        return $resultRedirect->setPath('*/*/edit', ['id' => $menus->getEntityId()]);
                    } catch (\Magento\Framework\Exception\LocalizedException $e) {
                        $this->messageManager->addErrorMessage($e->getMessage());
                    } catch (\RuntimeException $e) {
                        $this->messageManager->addErrorMessage($e->getMessage());
                    } catch (\Exception $e) {
                        $message = __('Something went wrong while saving the menus.');
                        $this->messageManager->addExceptionMessage($e, $message);
                    }
                } else {
                    $message = __('Please enter full information so you can create menus.');
                    $this->messageManager->addErrorMessage($message);
                    return $resultRedirect->setPath('*/*/new');
                }
            }
        }
        return $resultRedirect->setPath('menus/builder');
    }
    
    public function saveMenuItem($menu_items)
    {
        $menu_item = $this->menuBuilderItemFactory->create();
        $resourceItemMeta = $this->menuBuilderItemMetaFactory->create()->getResource();
        $i=1;
        foreach ($menu_items as $key => $items) {
            $id_item = $key;
            $menu_item = $menu_item->load($id_item);
            $menu_item->setItemTitle($items['title']);
            $menu_item->setMenuOrder($i);
            $menu_item->setStatus(1);
            $menu_item->save();
            if ($items['submenu_type'] == 'default_dropdown') {
                $items['submenu_columns'] = null;
                $items['submenu_bg_image'] = null;
                $items['background_repeat'] = null;
                $items['background_position'] = null;
                $items['background_size'] = null;
                $items['block_content'] = null;
            } elseif ($items['submenu_type'] == 'multicolumn_dropdown') {
                $items['block_content'] = null;
            } else {
                $items['submenu_columns'] = null;
                $items['submenu_bg_image'] = null;
                $items['background_repeat'] = null;
                $items['background_position'] = null;
                $items['background_size'] = null;
            }
            
            foreach ($items as $key_item => $item) {
                $menu_item_meta = $this->menuBuilderItemMetaFactory->create();
                $data_meta = $resourceItemMeta->getDataMeta($id_item, $key_item);
                if ($data_meta) {
                    $menu_item_meta = $menu_item_meta->load($data_meta['entity_id']);
                    $menu_item_meta->setMenuItemId($id_item);
                    $menu_item_meta->setMetaKey($key_item);
                    $menu_item_meta->setMetaValue($item);
                } else {
                    $menu_item_meta->setMenuItemId($id_item);
                    $menu_item_meta->setMetaKey($key_item);
                    $menu_item_meta->setMetaValue($item);
                }
                $menu_item_meta->save();
            }
            $i++;
        }
    }
}
