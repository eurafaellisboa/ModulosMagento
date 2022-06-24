<?php

namespace MGS\Blog\Controller\Adminhtml\Post;

use Magento\Framework\App\Filesystem\DirectoryList;
use MGS\Blog\Controller\Adminhtml\Blog;
use Magento\Framework\Exception\LocalizedException;

class Save extends Blog
{
    public function execute()
    {
        if ($this->getRequest()->getPostValue()) {
            try {
                $blogHelper = $this->_objectManager->create('MGS\Blog\Helper\Data');
                $model = $this->_objectManager->create('MGS\Blog\Model\Post');
                $data = $this->getRequest()->getPostValue();
                $inputFilter = new \Zend_Filter_Input(
                    [],
                    [],
                    $data
                );
                $data = $inputFilter->getUnescaped();
                $id = $this->getRequest()->getParam('post_id');
                if ($id) {
                    $model->load($id);
                    if ($id != $model->getId()) {
                        throw new LocalizedException(__('The wrong post is specified.'));
                    }
                }
                if (isset($_FILES['thumbnail']['name']) && $_FILES['thumbnail']['name'] != '') {
                    $uploader = $this->_objectManager->create(
                        'Magento\MediaStorage\Model\File\Uploader',
                        ['fileId' => 'thumbnail']
                    );
                    $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png', 'svg']);
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setAllowCreateFolders(true);
                    $uploader->setFilesDispersion(true);
                    $ext = pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION);
                    if ($uploader->checkAllowedExtension($ext)) {
                        $path = $this->_objectManager->get('Magento\Framework\Filesystem')->getDirectoryRead(DirectoryList::MEDIA)
                            ->getAbsolutePath('mgs_blog/');
                        $uploader->save($path);
                        $fileName = $uploader->getUploadedFileName();
                        if ($fileName) {
                            $data['post']['thumbnail'] = 'mgs_blog' . $fileName;
                        }
                    } else {
                        $this->messageManager->addError(__('Disallowed file type.'));
                        return $this->redirectToEdit($model, $data);
                    }
                } else {
                    if (isset($data['thumbnail']['delete']) && $data['thumbnail']['delete'] == 1) {
                        $data['post']['thumbnail'] = '';
                    } else {
                        unset($data['thumbnail']);
                    }
                }
                if (isset($_FILES['image']['name']) && $_FILES['image']['name'] != '') {
                    $uploader = $this->_objectManager->create(
                        'Magento\MediaStorage\Model\File\Uploader',
                        ['fileId' => 'image']
                    );
                    $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png', 'svg']);
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setAllowCreateFolders(true);
                    $uploader->setFilesDispersion(true);
                    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                    if ($uploader->checkAllowedExtension($ext)) {
                        $path = $this->_objectManager->get('Magento\Framework\Filesystem')->getDirectoryRead(DirectoryList::MEDIA)
                            ->getAbsolutePath('mgs_blog/');
                        $uploader->save($path);
                        $fileName = $uploader->getUploadedFileName();
                        if ($fileName) {
                            $data['post']['image'] = 'mgs_blog' . $fileName;
                        }
                    } else {
                        $this->messageManager->addError(__('Disallowed file type.'));
                        return $this->redirectToEdit($model, $data);
                    }
                } else {
                    if (isset($data['image']['delete']) && $data['image']['delete'] == 1) {
                        $data['post']['image'] = '';
                    } else {
                        unset($data['image']);
                    }
                }
                if (!isset($data['post']['url_key']) || $data['post']['url_key'] == '') {
                    $urlKey = $this->_objectManager->get('Magento\Catalog\Model\Product\Url')->formatUrlKey($data['post']['title']);
                    $existUrlKeys = $this->_objectManager->create('MGS\Blog\Model\Post')->getCollection()->addFieldToFilter('url_key', ['eq' => $urlKey]);
                    if (count($existUrlKeys)) {
                        $data['post']['url_key'] = $urlKey . '-' . rand();
                    } else {
                        $data['post']['url_key'] = $urlKey;
                    }
                }
                if (isset($data['post']['tags'])) {
                    if ($id) {
                        $model->load($id);
                        $originalTags = explode(',', $model->getTags());
                    } else {
                        $originalTags = array();
                    }
                    $tags = explode(',', $data['post']['tags']);
                    array_walk($tags, 'trim');
                    foreach ($tags as $key => $tag) {
                        $tags[$key] = $blogHelper->convertSlashes($tag, 'forward');
                    }
                    $tags = array_unique($tags);
                    $commonTags = array_intersect($tags, $originalTags);
                    $removedTags = array_diff($originalTags, $commonTags);
                    $addedTags = array_diff($tags, $commonTags);
                    if (count($tags)) {
                        $data['post']['tags'] = trim(implode(',', $tags));
                    } else {
                        $data['post']['tags'] = '';
                    }
                }
                $model->setData($data['post'])
                    ->setId($this->getRequest()->getParam('post_id'));
                if (isset($data['post']['created_at']) && $data['post']['created_at']) {
                    $model->setUpdatedAt($this->_objectManager->get('Magento\Framework\Stdlib\DateTime\DateTime')->gmtDate());
                } else {
                    $model->setCreatedAt($this->_objectManager->get('Magento\Framework\Stdlib\DateTime\DateTime')->gmtDate());
                    $model->setUpdatedAt($this->_objectManager->get('Magento\Framework\Stdlib\DateTime\DateTime')->gmtDate());
                }
               $userData = $this->_objectManager->get('Magento\Backend\Model\Auth\Session')->getUser()->getData();
				if (isset($data['post']['user']) && $data['post']['user']) {
					$model->setUpdatedByUser($userData['username']);
				} else {
					$model->setUser($userData['username']);
				}
				
                if (isset($data['categories'])) {
                    $model->setCategories($data['categories']);
                } else {
                    $model->setCategories(null);
                }
				
                $model->setStores($data['stores']);
                $session = $this->_objectManager->get('Magento\Backend\Model\Session');
                $session->setPageData($model->getData());
                
                if (isset($data['gallery_image'])) {
                    $model->setGalleryImage($data['gallery_image']);
                }
                
                $model->save();
                $this->messageManager->addSuccess(__('You saved the post.'));
                $session->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('blog/post/edit', ['post_id' => $model->getId()]);
                    return;
                }
                $this->_redirect('blog/post/index');
                return;
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                $id = (int)$this->getRequest()->getParam('post_id');
                if (!empty($id)) {
                    $this->_redirect('blog/post/edit', ['post_id' => $id]);
                } else {
                    $this->_redirect('blog/post/new');
                }
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('Something went wrong while saving the post data. Please review the error log.')
                );
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->_redirect('blog/post/edit', ['post_id' => $this->getRequest()->getParam('post_id')]);
                return;
            }
        }
        $this->_redirect('blog/post/index');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MGS_Blog::save_post');
    }
}
