<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MGS\Blog\Controller\Adminhtml\Post;

use Magento\Framework\App\Filesystem\DirectoryList;

class Upload extends \Magento\Backend\App\Action
{
	protected $_storeManager;
	
	protected $_filesystem;
	
	protected $_file;
	
	/**
     * File Uploader factory
     *
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $_fileUploaderFactory;
	
	public function __construct(
		\Magento\Backend\App\Action\Context $context,
		\Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
		\Magento\Framework\Filesystem\Driver\File $file,
		\Magento\Store\Model\StoreManagerInterface $storeManager
	)     
	{
		parent::__construct($context);
		
		$this->_storeManager = $storeManager;
		$this->_filesystem = $filesystem;
		$this->_file = $file;
        $this->_fileUploaderFactory = $fileUploaderFactory;
	}
    
    public function execute()
    {
		if($this->getRequest()->isAjax()){
			$result = ['result'=>'error', 'data'=>__('Can not upload file.')];
			if(isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {
				$uploader = $this->_fileUploaderFactory->create(['fileId' => 'file']);
				$file = $uploader->validateFile();
				
				if(($file['name']!='') && ($file['size'] >0)){
					$uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
					$uploader->setAllowRenameFiles(true);
					$uploader->setFilesDispersion(true);
					$path = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('mgs_blog/gallery/image/');
					$uploader->save($path);
					$fileName = $uploader->getUploadedFileName();
					if($this->isFile('mgs_blog/gallery/image/'.$fileName)){
						$result['result'] = 'success';
						$result['data'] = $fileName;
					}else{
						$result['data'] = $_FILES['file']['name'];
					}
				}
			}
			
			echo json_encode($result);
		}else{
			$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
			$resultRedirect->setUrl($this->_redirect->getRefererUrl());
			return $resultRedirect;
		}
    }
	
	public function isFile($filename)
    {
        $mediaDirectory = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA);

        return $mediaDirectory->isFile($filename);
    }
}