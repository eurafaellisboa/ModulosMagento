<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MGS\MollaTheme\Controller\Adminhtml\MollaTheme;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
class Import extends \MGS\Fbuilder\Controller\Adminhtml\Fbuilder
{
	protected $_sectionCollection;
	protected $_blockCollection;
	
	/**
     * Page factory
     *
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $_pageFactory;
	

	protected $_filesystem;
	protected $_fileUploaderFactory;
	protected $_file;
	
	/**
	 * @var \Magento\Framework\Xml\Parser
	 */
	private $_parser;
	
	protected $_xmlArray;
	protected $_generateHelper;

	
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
		\Magento\Backend\App\Action\Context $context,
		\MGS\Fbuilder\Model\ResourceModel\Section\CollectionFactory $sectionCollectionFactory,
		\MGS\Fbuilder\Model\ResourceModel\Child\CollectionFactory $blockCollectionFactory,
		\Magento\Cms\Model\PageFactory $pageFactory,
		\Magento\Framework\Xml\Parser $parser,
		\Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
		\MGS\Fbuilder\Helper\Generate $generateHelper,
		\Magento\Framework\Filesystem\Driver\File $file,
		\Magento\Framework\App\Config\Storage\WriterInterface $configWriter
	)
    {
        parent::__construct($context);
		$this->_sectionCollection = $sectionCollectionFactory;
		$this->_blockCollection = $blockCollectionFactory;
		$this->_pageFactory = $pageFactory;
		$this->_filesystem = $filesystem;
		$this->_file = $file;
        $this->_fileUploaderFactory = $fileUploaderFactory;
		$this->_parser = $parser;
		$this->_generateHelper = $generateHelper;
		$this->configWriter = $configWriter;
    }
	
	public function isLocalhost() {
        $whitelist = array(
            '127.0.0.1',
			'localhost',
			'127.0.0.1:8080',
			'localhost:8080',
            '::1'
        );
        
        return in_array($_SERVER['REMOTE_ADDR'], $whitelist);
    }

    /**
     * Edit sitemap
     *
     * @return void
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
		if($this->getRequest()->isAjax()){
			
			$item = '25724801';
			$theme = 'molla';
			$activeKey = $this->_objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('active_theme/activate/'.$theme);
			if(!$this->isLocalhost()) {
				if($activeKey==''){
					$result = ['result'=>'error', 'data'=>__('Please activate the theme first.')];
					return $this->getResponse()->setBody(json_encode($result));
				}else{
					$keyValue = trim($activeKey);
					$baseUrl = $this->_objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('web/unsecure/base_url');
					$domain = str_replace('http://','',$baseUrl);
					$domain = str_replace('https://','',$domain);
					
					$domain = trim(preg_replace('/^.*?\\/\\/(.*)?\\//', '$1', $domain));
	
					if(strpos($domain, "/")){
						$domain = substr($domain, 0, strpos($domain, "/"));
					}
					$magentoVersion =  $this->_objectManager->get('Magento\Framework\App\ProductMetadataInterface')->getVersion();
					
					$themeName = ucfirst($theme);
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, "https://www.magesolution.com/licensekey/index/activate/item/$item/theme/$themeName/key/$keyValue/domain/$domain/version/$magentoVersion");
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_USERAGENT, 'ACTIVATE-THEMEFOREST-THEME');
	
					$result = curl_exec($ch);
					curl_close($ch);
					if($result!='Activated'){
						$this->configWriter->save('active_theme/activate/'.$theme, NULL);
						$result = ['result'=>'error', 'data'=>__('The theme has not been activated or your purchase code has been used for another domain.')];
						return $this->getResponse()->setBody(json_encode($result));
					}
					
				}
			}

			
			if($pageId = $this->getRequest()->getParam('page_id')){
				$result = ['result'=>'error', 'data'=>__('Can not upload file.')];
			
				if(isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {
					$uploader = $this->_fileUploaderFactory->create(['fileId' => 'file']);
					$file = $uploader->validateFile();
					
					if(($file['name']!='') && ($file['size'] >0)){
						$uploader->setAllowedExtensions(['xml']);
						$uploader->setAllowRenameFiles(true);
						$path = $this->_filesystem->getDirectoryRead(DirectoryList::VAR_DIR)->getAbsolutePath('fbuilder_import');
						$uploader->save($path);
						$fileName = $uploader->getUploadedFileName();
						
						if($this->isFile('fbuilder_import/'.$fileName)){
							$dir = $this->_filesystem->getDirectoryRead(DirectoryList::VAR_DIR)->getAbsolutePath('fbuilder_import/');
							$importFile = $dir.$fileName;
							
							if (is_readable($importFile)){
								try {
									$this->_xmlArray = $this->_parser->load($importFile)->xmlToArray();
									
									// Remove old sections
									$sections = $this->_sectionCollection->create()
										->addFieldToFilter('page_id', $pageId);

									if (count($sections) > 0){
										foreach ($sections as $_section){
											$_section->delete();
										}
									}
									
									// Remove old blocks
									$childs = $this->_blockCollection->create()
										->addFieldToFilter('page_id', $pageId);

									if (count($childs) > 0){
										foreach ($childs as $_child){
											$_child->delete();
										}
									}
									
									$html = '';
									
									// Import new sections
									$sectionArray = $this->_xmlArray['page']['section'];
									if(isset($sectionArray)){
										if(isset($sectionArray[0]['name'])){
											foreach($sectionArray as $section){
												$section['store_id'] = 0;
												$section['page_id'] = $pageId;
												$this->_objectManager->create('MGS\Fbuilder\Model\Section')->setData($section)->save();
											}
										}else{
											$sectionArray['store_id'] = 0;
											$sectionArray['page_id'] = $pageId;
											$this->_objectManager->create('MGS\Fbuilder\Model\Section')->setData($sectionArray)->save();
										}
									}
									
									// Import new blocks
									$blockArray = $this->_xmlArray['page']['block'];
									if(isset($blockArray)){
										if(isset($blockArray[0]['block_name'])){
											foreach($blockArray as $block){
												$block['store_id'] = 0;
												$block['page_id'] = $pageId;
												$oldId = $block['child_id'];
												unset($block['child_id']);
												$child = $this->_objectManager->create('MGS\Fbuilder\Model\Child')->setData($block)->save();
												$customStyle = $child->getCustomStyle();
												$customStyle = str_replace('.block'.$oldId,'.block'.$child->getId(),$customStyle);
												$child->setCustomStyle($customStyle)->save();
											}
										}else{
											$blockArray['store_id'] = 0;
											$blockArray['page_id'] = $pageId;
											$oldId = $blockArray['child_id'];
											unset($blockArray['child_id']);
											$child = $this->_objectManager->create('MGS\Fbuilder\Model\Child')->setData($blockArray)->save();
											$customStyle = $child->getCustomStyle();
											$customStyle = str_replace('.block'.$oldId,'.block'.$child->getId(),$customStyle);
											$child->setCustomStyle($customStyle)->save();
										}
									}
									
									$this->_generateHelper->importContent($pageId);
									
									$this->generateBlockCss();
									
									$this->_eventManager->dispatch('mgs_fbuilder_import_before_end', ['content' => $this->_xmlArray]);
									
									$result['result'] = 'success';
								}catch (\Exception $e) {
									$result['result'] = $e->getMessage();
								}
							}else{
								$result['result'] = __('Cannot import page');
							}
							$result['data'] = $fileName;
						}
					}
				}
			}else{
				$result['result'] = __('Have no page to import');
			}

			return $this->getResponse()->setBody(json_encode($result));
		}

		$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
		$resultRedirect->setUrl($this->_redirect->getRefererUrl());
		return $resultRedirect;
		
		
    }
	
	public function generateBlockCss(){
		$model = $this->_objectManager->create('MGS\Fbuilder\Model\Child');
		$collection = $model->getCollection();
		$customStyle = '';
		foreach($collection as $child){
			if($child->getCustomStyle() != ''){
				$customStyle .= $child->getCustomStyle();
			}
		}
		if($customStyle!=''){
			try{
				$this->_generateHelper->generateFile($customStyle, 'blocks.css', $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('mgs/fbuilder/css/'));
			}catch (\Exception $e) {
				
			}
		}
	}
	
	public function isFile($filename)
    {
        $mediaDirectory = $this->_filesystem->getDirectoryRead(DirectoryList::VAR_DIR);

        return $mediaDirectory->isFile($filename);
    }
}
