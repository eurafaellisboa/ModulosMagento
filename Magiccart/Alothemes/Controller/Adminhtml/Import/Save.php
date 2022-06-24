<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2016-01-05 10:40:51
 * @@Modify Date: 2018-11-12 14:52:26
 * @@Function:
 */

namespace Magiccart\Alothemes\Controller\Adminhtml\Import;

use Magento\Framework\App\Filesystem\DirectoryList;

class Save extends \Magiccart\Alothemes\Controller\Adminhtml\Action
{


    protected $_store    = 0;
    protected $_filePath = '';
    protected $_dir = '';
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $resultRedirect = $this->_resultRedirectFactory->create();
        
        if($this->getRequest()->getParam('theme_path')) $this->ImportXml();

        return $resultRedirect->setPath('*/*/index');
    }

    public function ImportXml()
    {
        $theme_path      = $this->getRequest()->getParam('theme_path');
        $this->_filePath = sprintf(self::CMS, $theme_path);
        $this->_dir      = $this->_filesystem->getDirectoryWrite(DirectoryList::APP);
        $request = $this->getRequest()->getParams();
        $stores = isset($request['store_ids']) ? $request['store_ids'] : array(0);
        $scope  = 'default';
        if(isset($request['scope']) && isset($request['scope_id'])){
            $scope = $request['scope'];
            if($request['scope'] == 'websites'){
                $stores = $this->_storeManager->getWebsite($request['scope_id'])->getStoreIds();
            }else {
                $stores  = $request['scope_id']; 
            }
        }
        $this->_store = is_array($stores) ? $stores : explode(',', $stores);
        if($request['action']){
            if( isset($request['block']) && $request['block'] )   $this->ImportBlock(isset($request['overwrite_block']));
            if( isset($request['page'])  && $request['page'] )    $this->ImportPage(isset($request['overwrite_page']));
            if( isset($request['config'])  && $request['config'] )  $this->ImportSystem($scope);
            $this->ImportMagicmenu();
            $this->ImportMagicproduct();            
            $this->ImportMagicslider();            
            $this->ImportLookbook();            
        } else {
            $this->messageManager->addSuccess(__('This feature not available.'));
        }

    }

    public function ImportBlock($overwrite=false)
    {
        $fileName = 'block.xml';
        $filePath = $this->_filePath .$fileName;
        $backupFilePath = $this->_dir->getAbsolutePath($filePath);
        $storeIds = $this->_store;
        try{
            if (!is_readable($backupFilePath)) throw new \Exception(__("Can't read data file: %1", $backupFilePath));
            $xmlObj = new \Magento\Framework\Simplexml\Config($backupFilePath);
            $num = 0;
            $block = $xmlObj->getNode('block');
            if($block){
                foreach ($block->children() as $item){
                    //Check if Block already exists
                    $collection = $this->_objectManager->create('\Magento\Cms\Model\ResourceModel\Block\Collection');
                    $oldBlocks = $collection->addFieldToFilter('identifier', $item->identifier)->addStoreFilter($storeIds);
                    
                    //If items can be overwritten
                    if ($overwrite){
                        if (count($oldBlocks) > 0){
                            $conflictingOldItems[] = $item->identifier;
                            foreach ($oldBlocks as $old) $old->delete();
                        }
                    }else {
                        if (count($oldBlocks) > 0){
                            $conflictingOldItems[] = $item->identifier;
                            continue;
                        }
                    }
                    $model = $this->_objectManager->create('Magento\Cms\Model\Block');
                    $model->setData($item->asArray())->setStores($storeIds)->save();
                    $num++;
                }               
            }

            $this->messageManager->addSuccess(__('Import (%1) Item(s) in file "%2".', $num, $backupFilePath));  

        } catch (\Exception $e) {
                $this->messageManager->addError(__('Can not import file "%1".<br/>"%2"', $backupFilePath, $e->getMessage()));
        }
    }

    public function ImportPage($overwrite=false)
    {
        $fileName = 'page.xml';
        $filePath = $this->_filePath .$fileName;
        $backupFilePath = $this->_dir->getAbsolutePath($filePath);
        $storeIds = $this->_store;
        try{
            if (!is_readable($backupFilePath)) throw new \Exception(__("Can't read data file: %1", $backupFilePath));
            $xmlObj = new \Magento\Framework\Simplexml\Config($backupFilePath);
            $num = 0;
            $page = $xmlObj->getNode('page');
            if($page){
                foreach ($page->children() as $item){
                    //Check if Block already exists
                    $collection = $this->_objectManager->create('\Magento\Cms\Model\ResourceModel\Page\Collection');
                    $oldPages = $collection->addFieldToFilter('identifier', $item->identifier)->addStoreFilter($storeIds);
                    
                    //If items can be overwritten
                    if ($overwrite){
                        if (count($oldPages) > 0){
                            $conflictingOldItems[] = $item->identifier;
                            foreach ($oldPages as $old) $old->delete();
                        }
                    }else {
                        if (count($oldPages) > 0){
                            $conflictingOldItems[] = $item->identifier;
                            continue;
                        }
                    }
                    $model = $this->_objectManager->create('Magento\Cms\Model\Page');
                    $model->setData($item->asArray())->setStores($storeIds)->save();
                    $num++;
                }               
            }

            $this->messageManager->addSuccess(__('Import (%1) Item(s) in file "%2".', $num, $backupFilePath));  

        } catch (\Exception $e) {
                $this->messageManager->addError(__('Can not import file "%1".<br/>"%2"', $backupFilePath, $e->getMessage()));
        }        
    }

    public function ImportSystem($scope='default')
    {
        $fileName = 'system.xml';
        $filePath = $this->_filePath .$fileName;
        $backupFilePath = $this->_dir->getAbsolutePath($filePath);
        $storeIds = $this->_store;
        try{

            if (!is_readable($backupFilePath)) throw new \Exception(__("Can't read data file: %1", $backupFilePath));
            $xmlObj = new \Magento\Framework\Simplexml\Config($backupFilePath);
            $num = 0;
            $system = $xmlObj->getNode('system');
            if($system){
                $model = $this->_objectManager->create('Magento\Config\Model\ResourceModel\Config');
                $request = $this->getRequest()->getParams();
                foreach ($system->children() as $item){
                    $node = $item->asArray();
                    if(!is_array($storeIds)) $storeIds = array($storeIds);
                    foreach ($storeIds as $storeId) {
                        if(isset($request['usewebsite'])){
                            $oldConfig = $this->_scopeConfig->getValue( $node['path'], $scope, $storeId );
                            if($oldConfig == $node['value']) continue;                           
                        }
                        $model->saveConfig($item->path, $node['value'], $scope, $storeId);
                        $num++;
                    }  
                }              
            }

            $themePath = $xmlObj->getNode('theme');
            $themeId = 0;
            $collection = $this->_objectManager->create('Magento\Theme\Model\Theme')->getCollection();
            foreach ($collection as $item) {
                if($themePath == $item->getData('theme_path')){
                    $themeId = $item->getData('theme_id');
                    break;
                } 
            }
            if($themeId){
                if(is_array($storeIds)){
                    foreach ($storeIds as $storeId) {
                        $model->saveConfig('design/theme/theme_id', $themeId, $scope, $storeId);
                        $num++;
                    }
                } else {
                    $model->saveConfig('design/theme/theme_id', $themeId, $scope, $storeIds);
                    $num++;
                }
      
            }
            $this->messageManager->addSuccess(__('Import (%1) Item(s) in file "%2".', $num, $backupFilePath));             

        } catch (\Exception $e) {
                $this->messageManager->addError(__('Can not import file "%1".<br/>"%2"', $backupFilePath, $e->getMessage()));
        }
        
    }

    public function ImportMagicmenu()
    {
        $fileName = 'magicmenu.xml';
        $filePath = $this->_filePath .$fileName;
        $backupFilePath = $this->_dir->getAbsolutePath($filePath);
        $storeIds = $this->_store;
        try{
            if (!is_readable($backupFilePath)) throw new \Exception(__("Can't read data file: %1", $backupFilePath));
            $xmlObj = new \Magento\Framework\Simplexml\Config($backupFilePath);
            $num = 0;
            $magicmenu = $xmlObj->getNode('magicmenu');
            if($magicmenu){
                foreach ($magicmenu->children() as $item){
                    //Check if Extra Menu already exists
                    $collection = $this->_objectManager->create('Magiccart\Magicmenu\Model\ResourceModel\Magicmenu\Collection');
                    $oldMenus   =  $collection->addFieldToFilter('link', $item->link)->load();
                    //If items can be overwritten
                    $overwrite = false; // get in cfg
                    if ($overwrite){
                        if (count($oldMenus) > 0){
                            foreach ($oldMenus as $old) $old->delete();
                        }
                    }else {
                        if (count($oldMenus) > 0){
                            continue;
                        }
                    }
                    $model = $this->_objectManager->create('Magiccart\Magicmenu\Model\Magicmenu');
                    $model->setData($item->asArray())->setStores(implode(',', $storeIds))->save();
                    $num++;
                }               
            }

            $this->messageManager->addSuccess(__('Import (%1) Item(s) in file "%2".', $num, $backupFilePath));              

        } catch (\Exception $e) {
                $this->messageManager->addError(__('Can not import file "%1".<br/>"%2"', $backupFilePath, $e->getMessage()));
        }
    }

    public function ImportMagicproduct()
    {
        $fileName = 'magicproduct.xml';
        $filePath = $this->_filePath .$fileName;
        $backupFilePath = $this->_dir->getAbsolutePath($filePath);
        $storeIds = $this->_store;
        try{
            if (!is_readable($backupFilePath)) throw new \Exception(__("Can't read data file: %1", $backupFilePath));
            $xmlObj = new \Magento\Framework\Simplexml\Config($backupFilePath);
            $num = 0;
            $magicproduct = $xmlObj->getNode('magicproduct');
            if($magicproduct){
                foreach ($magicproduct->children() as $item){
                    //Check if Magicproduct already exists
                    $collection = $this->_objectManager->create('Magiccart\Magicproduct\Model\ResourceModel\Magicproduct\Collection');
                    $oldMenus   =  $collection->addFieldToFilter('identifier', $item->identifier)->addFieldToFilter('type_id', $item->type_id)->load();
                    //If items can be overwritten
                    $overwrite = false; // get in cfg
                    if ($overwrite){
                        if (count($oldMenus) > 0){
                            foreach ($oldMenus as $old) $old->delete();
                        }
                    }else {
                        if (count($oldMenus) > 0){
                            continue;
                        }
                    }
                    $model = $this->_objectManager->create('Magiccart\Magicproduct\Model\Magicproduct');   
                    $model->setData($item->asArray())->save();
                    $num++;
                }               
            }

            $this->messageManager->addSuccess(__('Import (%1) Item(s) in file "%2".', $num, $backupFilePath));

        } catch (\Exception $e) {
                $this->messageManager->addError(__('Can not import file "%1".<br/>"%2"', $backupFilePath, $e->getMessage()));
        }
    }

    public function ImportMagicslider()
    {
        $fileName = 'magicslider.xml';
        $filePath = $this->_filePath .$fileName;
        $backupFilePath = $this->_dir->getAbsolutePath($filePath);
        $storeIds = $this->_store;
        try{
            if (!is_readable($backupFilePath)) throw new \Exception(__("Can't read data file: %1", $backupFilePath));
            $xmlObj = new \Magento\Framework\Simplexml\Config($backupFilePath);
            $num = 0;
            $magicproduct = $xmlObj->getNode('magicslider');
            if($magicproduct){
                foreach ($magicproduct->children() as $item){
                    //Check if Magicproduct already exists
                    $collection = $this->_objectManager->create('Magiccart\Magicslider\Model\ResourceModel\Magicslider\Collection');
                    $oldMenus   =  $collection->addFieldToFilter('identifier', $item->identifier)->load();
                    //If items can be overwritten
                    $overwrite = false; // get in cfg
                    if ($overwrite){
                        if (count($oldMenus) > 0){
                            foreach ($oldMenus as $old) $old->delete();
                        }
                    }else {
                        if (count($oldMenus) > 0){
                            continue;
                        }
                    }
                    $model = $this->_objectManager->create('Magiccart\Magicslider\Model\Magicslider');   
                    $model->setData($item->asArray())->save();
                    $num++;
                }               
            }

            $this->messageManager->addSuccess(__('Import (%1) Item(s) in file "%2".', $num, $backupFilePath));

        } catch (\Exception $e) {
                $this->messageManager->addError(__('Can not import file "%1".<br/>"%2"', $backupFilePath, $e->getMessage()));
        }
    }

    public function ImportLookbook()
    {
        $fileName = 'lookbook.xml';
        $filePath = $this->_filePath .$fileName;
        $backupFilePath = $this->_dir->getAbsolutePath($filePath);
        $storeIds = $this->_store;
        try{
            if (!is_readable($backupFilePath)){
                return;
                throw new \Exception(__("Can't read data file: %1", $backupFilePath));
            } 
            $xmlObj = new \Magento\Framework\Simplexml\Config($backupFilePath);
            $num = 0;
            $magicproduct = $xmlObj->getNode('lookbook');
            if($magicproduct){
                foreach ($magicproduct->children() as $item){
                    //Check if Magicproduct already exists
                    $collection = $this->_objectManager->create('Magiccart\Lookbook\Model\ResourceModel\Lookbook\Collection');
                    $oldMenus   =  $collection->addFieldToFilter('identifier', $item->identifier)->load();
                    //If items can be overwritten
                    $overwrite = false; // get in cfg
                    if ($overwrite){
                        if (count($oldMenus) > 0){
                            foreach ($oldMenus as $old) $old->delete();
                        }
                    }else {
                        if (count($oldMenus) > 0){
                            continue;
                        }
                    }
                    $model = $this->_objectManager->create('Magiccart\Lookbook\Model\Lookbook');   
                    $model->setData($item->asArray())->save();
                    $num++;
                }               
            }

            $this->messageManager->addSuccess(__('Import (%1) Item(s) in file "%2".', $num, $backupFilePath));

        } catch (\Exception $e) {
                $this->messageManager->addError(__('Can not import file "%1".<br/>"%2"', $backupFilePath, $e->getMessage()));
        }
    }

}
