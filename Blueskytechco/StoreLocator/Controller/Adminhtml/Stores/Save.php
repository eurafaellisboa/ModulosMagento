<?php
/**
 * Copyright © 2019 Blueskytechco. All rights reserved. 
 */

namespace Blueskytechco\StoreLocator\Controller\Adminhtml\Stores;

use \Blueskytechco\StoreLocator\Controller\Adminhtml\Stores;
use \Magento\Backend\App\Action\Context;
use \Magento\Framework\View\Result\PageFactory;
use \Magento\Framework\App\Filesystem\DirectoryList;
use \Blueskytechco\StoreLocator\Api\StoreRepositoryInterface;
use \Blueskytechco\StoreLocator\Helper\Config as ConfigHelper;
use \Magento\PageCache\Model\Config;
use \Magento\Framework\App\Cache\TypeListInterface;
use \Blueskytechco\StoreLocator\Api\Data\StoreInterfaceFactory;

class Save extends Stores
{

    private $config;
    private $typeList;
	private $_countryFactory;


    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
		\Magento\Directory\Model\CountryFactory $countryFactory,
        StoreRepositoryInterface $storeRepository,
        StoreInterfaceFactory $storeFactory,
        ConfigHelper $configHelper,
        Config $config,
        TypeListInterface $typeList
    ) {
        $this->config = $config;
        $this->typeList = $typeList;
		$this->_countryFactory = $countryFactory;
        parent::__construct($context, $resultPageFactory, $storeRepository, $storeFactory, $configHelper);
    }

    public function execute()
    {
		$time = json_encode($this->getRequest()->getPostValue('time'));
		if(isset($_FILES)){
			$img_arr = '';
			if(isset($_FILES['image_stored']['error']) && $_FILES['image_stored']['error'] == '0'){
				try { 
					$uploader = $this->_objectManager->create(
						'Magento\Framework\File\Uploader',
						['fileId' => 'image_stored']
					);
					$uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
					$uploader->setAllowRenameFiles(true);
					$uploader->setFilesDispersion(true);
					$mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
						->getDirectoryRead(DirectoryList::MEDIA);	
					$path_ = 'image_stored/image';
					$result = $uploader->save($mediaDirectory->getAbsolutePath($path_));
					$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
					$img_arr = $path_.$result['file'];
					
				} catch (\Exception $e) { 
				}
				$dataImage = json_encode($img_arr); 
				$this->getRequest()->setPostValue('image_store', $dataImage);		
			}
		}
		$this->getRequest()->setPostValue('time_store', $time);
		$country_code = $this->getRequest()->getPostValue('country');
		$country = $this->_countryFactory->create()->loadByCode($country_code);
        $country_name = $country->getName();
		$this->getRequest()->setPostValue('country_name', $country_name);
        $data = $this->getRequest()->getPostValue();
        if ($this->config->isEnabled()) {
            $this->typeList->invalidate('full_page');
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $store = $this->storeFactory->create();
            $store->setData($data);
            $this->_eventManager->dispatch(
                'storelocator_store_prepare_save',
                ['store' => $store, 'request' => $this->getRequest()]
            );
            try {
                $this->storeRepository->save($store);
                $this->messageManager->addSuccessMessage(__('The store has been saved.'));
                $this->_getSession()->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['store_id' => $store->getId(), '_current' => true, 'active_tab' => 'store_info']);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the store.'));
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['store_id' => $this->getRequest()->getParam('store_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    protected function _isAllowed()
    {
        return true;
    }
}
