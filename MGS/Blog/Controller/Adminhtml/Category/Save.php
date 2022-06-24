<?php

namespace MGS\Blog\Controller\Adminhtml\Category;

use Magento\Framework\App\Filesystem\DirectoryList;
use MGS\Blog\Controller\Adminhtml\Blog;
use Magento\Framework\Exception\LocalizedException;

class Save extends Blog
{
    public function execute()
    {
        if ($this->getRequest()->getPostValue()) {
            try {
                $model = $this->_objectManager->create('MGS\Blog\Model\Category');
                $data = $this->getRequest()->getPostValue();
                $inputFilter = new \Zend_Filter_Input(
                    [],
                    [],
                    $data
                );
                $data = $inputFilter->getUnescaped();
                $id = $this->getRequest()->getParam('category_id');
                if ($id) {
                    $model->load($id);
                    if ($id != $model->getId()) {
                        throw new LocalizedException(__('The wrong category is specified.'));
                    }
                }
                if (!isset($data['category']['url_key']) || $data['category']['url_key'] == '') {
                    $urlKey = $this->_objectManager->get('Magento\Catalog\Model\Product\Url')->formatUrlKey($data['category']['title']);
                    $existUrlKeys = $this->_objectManager->create('MGS\Blog\Model\Category')->getCollection()->addFieldToFilter('url_key', ['eq' => $urlKey]);
                    if (count($existUrlKeys)) {
                        $data['category']['url_key'] = $urlKey . '-' . rand();
                    } else {
                        $data['category']['url_key'] = $urlKey;
                    }
                }
                $model->setData($data['category'])
                    ->setId($this->getRequest()->getParam('category_id'));
                $model->setStores($data['stores']);
                $session = $this->_objectManager->get('Magento\Backend\Model\Session');
                $session->setPageData($model->getData());
                $model->save();
                $this->messageManager->addSuccess(__('You saved the category.'));
                $session->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('blog/category/edit', ['category_id' => $model->getId()]);
                    return;
                }
                $this->_redirect('blog/category/index');
                return;
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                $id = (int)$this->getRequest()->getParam('category_id');
                if (!empty($id)) {
                    $this->_redirect('blog/category/edit', ['category_id' => $id]);
                } else {
                    $this->_redirect('blog/category/new');
                }
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('Something went wrong while saving the category data. Please review the error log.')
                );
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->_redirect('blog/category/edit', ['category_id' => $this->getRequest()->getParam('category_id')]);
                return;
            }
        }
        $this->_redirect('blog/category/index');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MGS_Blog::save_category');
    }
}
