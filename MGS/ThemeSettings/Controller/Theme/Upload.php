<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
 
namespace MGS\ThemeSettings\Controller\Theme;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\ResultFactory;
class Upload extends \MGS\ThemeSettings\Controller\Theme
{
	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\MGS\Fbuilder\Helper\Data $builderHelper,
		\Magento\Framework\App\Cache\Manager $cacheManager,
		\Magento\Framework\Filesystem $filesystem,
		\Magento\Framework\Xml\Parser $parser,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\MGS\ThemeSettings\Helper\Config $themeSettingConfig,
		\Magento\Framework\Filesystem\Io\File $ioFile,
		\Magento\Framework\Session\SessionManagerInterface $session,
		\Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
	)     
	{
		$this->_fileUploaderFactory = $fileUploaderFactory;
		parent::__construct($context, $builderHelper, $cacheManager, $filesystem, $parser, $storeManager, $themeSettingConfig, $ioFile, $session);
	}
    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
		if($this->getRequest()->isAjax()){
			if($this->builderHelper->showButton() && $this->session->getThemeCustomize()){
				$data = $this->getRequest()->getPostValue();
				$settingPath = $data['id'];
				/* if(isset($data['store_path'])){
					$store_path = $data['store_path'];
				}else{
					$store_path = 'default';
				} */
				
				$store_path = 'stores/'.$this->_storeManager->getStore()->getId();
				
				$result = ['result'=>'error', 'data'=>__('Can not upload file.')];

				if(isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {
					$uploader = $this->_fileUploaderFactory->create(['fileId' => 'file']);
					$file = $uploader->validateFile();
					
					if(($file['name']!='') && ($file['size'] >0)){
						$path = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath($data['save_path'].'/'.$store_path);
						$uploader->setAllowRenameFiles(true);
						$uploader->save($path);
						$fileName = $uploader->getUploadedFileName();
						if($this->isFile($data['save_path'].'/'.$store_path.'/'.$fileName)){
							$result['result'] = 'success';
							$result['data'] = $this->themeSetting->getMediaUrl($data['save_path'].'/'.$store_path.'/'.$fileName);
							
							$settingValue = $store_path.'/'.$fileName;
							$this->generateSettingTemp($settingPath, $settingValue);
							if($data['style']==1){
								$styleInline = $this->themeSetting->getStyleInline($this->_storeManager->getStore()->getId());
								$this->session->setStyleInline($styleInline);
								$result['style'] = $styleInline;
							}
						}else{
							$result['data'] = $_FILES['file']['name'];
						}
					}
				}
				$this->refreshCaches(['full_page']);
				return $this->getResponse()->setBody(json_encode($result));
				
			}
		}else{
			$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
			$resultRedirect->setPath('');
			return $resultRedirect;
		}
    }
	
	public function isFile($filename)
    {
        $mediaDirectory = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA);

        return $mediaDirectory->isFile($filename);
    }
}
