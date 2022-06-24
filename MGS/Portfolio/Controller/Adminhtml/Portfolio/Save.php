<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MGS\Portfolio\Controller\Adminhtml\Portfolio;

class Save extends \MGS\Portfolio\Controller\Adminhtml\Portfolio
{
    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if data sent
        $data = $this->getRequest()->getPostValue();
		$model = $this->_objectManager->create('MGS\Portfolio\Model\Portfolio');
        if ($data) {
            $id = $this->getRequest()->getParam('id');
			$model->load($id);
            $new = false;
            if (!$model->getId() && $id) {
                $this->messageManager->addError(__('This item no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }
			
			// Check Identifier
			
			if (empty($data['identifier'])) {
				$identifier = str_replace(' ', '-',strtolower(trim($data['name'])));
			}else {
				$identifier = $data['identifier'];
			}
			
			if($new){
				$data['identifier'] = $this->checkIdentifier($identifier);
			}else {
				if ($identifier == $model->getIdentifier()) {
					$data['identifier'] = $identifier;
				}else {
					$data['identifier'] = $this->checkIdentifier($identifier);
				}
				
			}
			
            // init model and set data

            $model->setData($data);
			
			$identifier = $data['identifier'];
			if($identifier!=''){
				$searchCat = $this->_objectManager->create('MGS\Portfolio\Model\Category')
					->getCollection()
					->addFieldToFilter('identifier', $identifier);
				
				$searchPortfolio = $this->_objectManager->create('MGS\Portfolio\Model\Portfolio')
					->getCollection()
					->addFieldToFilter('identifier', $identifier);
					
				if(isset($data['portfolio_id']) && $data['portfolio_id']!=''){
					$searchPortfolio->addFieldToFilter('portfolio_id', ['neq'=>$data['portfolio_id']]);
				}
				
				if((count($searchCat)>0) || (count($searchPortfolio)>0)){
					$data['identifier'] = $this->checkIdentifier($identifier);
				}
			}

            // try to save it
            try {
                // save the data
                
				$model->setStoreIds($this->getRequest()->getParam('stores', []));
				
				$model->save();
                // display success message
                $this->messageManager->addSuccess(__('You saved the item.'));
                // clear previously saved data from session
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);

                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
                }
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                // save data in session
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData($data);
                // redirect to edit form
                return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
            }
        }
        
		

		return $resultRedirect->setPath('*/*/');
    }
	
	public function checkIdentifier($identifier){
	
		$modelCheck = $this->_objectManager
							->create('MGS\Portfolio\Model\Portfolio')
							->getCollection()
							->addFieldToFilter ('identifier', $identifier);
		if(count($modelCheck)){
			
			$newModelCheck = $identifier . '-' . rand(1,100);
			return $this->checkIdentifier($newModelCheck);
		}
		
		return $identifier;
	}
}

