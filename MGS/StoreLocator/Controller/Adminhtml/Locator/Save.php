<?php

namespace MGS\StoreLocator\Controller\Adminhtml\Locator;

use MGS\StoreLocator\Model\StoreFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;

class Save extends \Magento\Backend\App\Action {

    protected $_storeFactory;
    protected $_coreRegistry;
    protected $_storeManager;
    protected $_filesystem;
    protected $_fileUploaderFactory;

    public function __construct(Context $context, 
            StoreFactory $storeFactory, 
            \Magento\Framework\Registry $coreRegistry, 
            \Magento\Store\Model\StoreManagerInterface $storeManager,
            \Magento\Framework\Filesystem $filesystem,
            \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory) {
        parent::__construct($context);
        $this->_storeFactory = $storeFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->_storeManager = $storeManager;
        $this->_filesystem = $filesystem;
        $this->_fileUploaderFactory = $fileUploaderFactory;
    }

    /**
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute() {
        $data = $this->getRequest()->getPostValue();
        if (!$data) {
            $this->_redirect('locator/*/');
            return;
        }

        $model = $this->_objectManager->create('MGS\StoreLocator\Model\Store');
        $new = true;
        if (!empty($data['id'])) {
            $model->load($data['id']);
			$new = false;
        }
        if (!empty($data['id']) && $model->isObjectNew()) {
            $this->messageManager->addError(__('This Locator no longer exists.'));
            $this->_redirect('locator/*/');
            return;
        }
		// Store Url
		if (empty($data['store_url'])) {
			$url = str_replace(' ', '-',strtolower(trim($data['name'])));
		}else {
			$url = $data['store_url'];
		}
		
		if($new){
			$data['store_url'] = $this->checkStoreUrl($url);
		}else {
			if ($url == $model->getStoreUrl()) {
				$data['store_url'] = $url;
			}else {
				$data['store_url'] = $this->checkStoreUrl($url);
			}
			
		}
		
        // Store Image upload
        if(isset($_FILES['image']['name']) && $_FILES['image']['name'] != '') {
            /** @var $uploader \Magento\MediaStorage\Model\File\Uploader */
            $uploader = $this->_fileUploaderFactory->create(['fileId' => 'image']);
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
            $uploader->setAllowRenameFiles(true);
            $uploader->setAllowCreateFolders(true);
            $uploader->setFilesDispersion(true);
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            if($uploader->checkAllowedExtension($ext)) {
                $path = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)
                        ->getAbsolutePath('mgs_storelocator/');
                $uploader->save($path);

                $fileName = $uploader->getUploadedFileName();
                if ($fileName) {
                    $data['image'] = 'mgs_storelocator'.$fileName;
                }
            } else {
                $this->messageManager->addError(__('Disallowed file type.'));
                return $this->redirectToEdit($model, $data);
            }
        } else {
            if(isset($data['image']['delete']) && $data['image']['delete'] == 1) {
                $data['image'] = '';
            } else {
                unset($data['image']);
            }
        }
		
		// Store logo upload
        if(isset($_FILES['logo_store']['name']) && $_FILES['logo_store']['name'] != '') {
            /** @var $uploader \Magento\MediaStorage\Model\File\Uploader */
            $uploader = $this->_fileUploaderFactory->create(['fileId' => 'logo_store']);
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
            $uploader->setAllowRenameFiles(true);
            $uploader->setAllowCreateFolders(true);
            $uploader->setFilesDispersion(true);
            $ext = pathinfo($_FILES['logo_store']['name'], PATHINFO_EXTENSION);
            if($uploader->checkAllowedExtension($ext)) {
                $path = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)
                        ->getAbsolutePath('mgs_storelocator/');
                $uploader->save($path);

                $fileName = $uploader->getUploadedFileName();
                if ($fileName) {
                    $data['logo_store'] = 'mgs_storelocator'.$fileName;
                }
            } else {
                $this->messageManager->addError(__('Disallowed file type.'));
                return $this->redirectToEdit($model, $data);
            }
        } else {
            if(isset($data['logo_store']['delete']) && $data['logo_store']['delete'] == 1) {
                $data['logo_store'] = '';
            } else {
                unset($data['logo_store']);
            }
        }
		
        $model->setData($data);
        try {
            $model->setStoreIds($this->getRequest()->getParam('stores', []));
            $model->save();
            $this->messageManager->addSuccess(__('You saved the store locator.'));
            $this->_getSession()->setLocator(false);
            $back = $this->getRequest()->getParam('back', false);
            if($back == 'edit') {
                return $this->_redirect('locator/*/edit', ['id' => $model->getId(), '_current' => true, 'active_tab' => '']);
            }
            $this->_redirect('locator/*/');
        } catch (\Exception $e) {
            $messages = $e->getMessages();
            $this->messageManager->addMessages($messages);
            $this->redirectToEdit($model, $data);
        }
    }

    /**
     * @param MGS\StoreLocator\Model\StoreFactory $model
     * @param array $data
     * @return void
     */
    protected function redirectToEdit($model, array $data) {
        $this->_getSession()->setLocator($data);
        $arguments = $model->getId() ? ['id' => $model->getId()] : [];
        $arguments = array_merge($arguments, ['_current' => true, 'active_tab' => '']);
        $this->_redirect('locator/*/edit', $arguments);
    }
	public function checkStoreUrl($urlKey){
	
		$modelCheck = $this->_objectManager
							->create('MGS\StoreLocator\Model\Store')
							->getCollection()
							->addFieldToFilter ('store_url', $urlKey);
		if(count($modelCheck)){
			
			$newModelCheck = $urlKey . '-' . rand(1,100);
			return $this->checkStoreUrl($newModelCheck);
		}
		
		return $urlKey;
	}
}
