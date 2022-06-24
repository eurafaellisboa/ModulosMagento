<?php
namespace Blueskytechco\SetProduct\Controller\Adminhtml\ProductSet;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Helper\Js;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Blueskytechco\SetProduct\Controller\Adminhtml\ProductAction;
use RuntimeException;

class Save extends ProductAction
{

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data = $this->getRequest()->getPost()) {
			$lookbook = $this->initLookbook();
			$data = $this->getRequest()->getPostValue();
			try {
				$uploader = $this->_objectManager->create(
					'Magento\MediaStorage\Model\File\Uploader',
					['fileId' => 'banner_image']
				);
				$uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
				$imageAdapter = $this->_objectManager->get('Magento\Framework\Image\AdapterFactory')->create();

				$uploader->addValidateCallback('banner_image', $imageAdapter, 'validateUploadFile');
				$uploader->setAllowRenameFiles(true);
				$uploader->setFilesDispersion(true);

				$mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')->getDirectoryRead(DirectoryList::MEDIA);
				$result = $uploader->save($mediaDirectory->getAbsolutePath(\Blueskytechco\SetProduct\Model\ProductSet::BASE_MEDIA_PATH));
				$data['banner_image'] = \Blueskytechco\SetProduct\Model\ProductSet::BASE_MEDIA_PATH . $result['file'];
			} catch (\Exception $e) {
				if(!$this->getRequest()->getPostValue('entity_id')){
					$data['banner_image'] = '//placehold.jp/1aada3/fff/1000x1000.png?text=Image';
				}
			}
			if(isset($data['products']) && is_array($data['products']) && !empty($data['products'])){
                $data['product_data'] = json_encode($data['products']);
			}

			if(!$this->getRequest()->getPostValue('entity_id')){
				$data['identifier'] = time().rand();
			}

            $this->prepareData($lookbook, $data);
            $this->_eventManager->dispatch('blueskytechco_setproduct_prepare_save', [ 
                'post'    => $lookbook,
                'request' => $this->getRequest()
            ]);
            try { 
                $lookbook->save();
				$data = $this->getRequest()->getPost();
				if(isset($data['entity_id']) && $data['entity_id']){
					$this->messageManager->addSuccessMessage(__('Edit success.'));
				}else{
					$this->messageManager->addSuccessMessage(__('Add success.'));
				}
                $this->_getSession()->setData('blueskytechco_setproduct_data', false);
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the.'));
            }
        }
        $resultRedirect->setPath('addproductsset/*/');

        return $resultRedirect;
    }
	
    protected function prepareData($lookbook, $data = [])
    {
        if ($lookbook->getCreatedAt() === null) {
            $data['created_at'] = $this->date->date();
        }

        $data['updated_at'] = $this->date->date();
        $lookbook->addData($data);

        return $this;
    }
}
