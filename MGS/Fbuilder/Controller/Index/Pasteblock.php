<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
 
namespace MGS\Fbuilder\Controller\Index;

use Magento\Framework\Controller\ResultFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Cache\Manager as CacheManager;
use Magento\Framework\App\Filesystem\DirectoryList;
class Pasteblock extends \Magento\Framework\App\Action\Action
{
	/**
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

	protected $builderHelper;
	protected $_filesystem;


	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		CustomerSession $customerSession,
		\Magento\Framework\View\Element\Context $urlContext,
		\Magento\Framework\Filesystem $filesystem,
		CacheManager $cacheManager,
		\MGS\Fbuilder\Helper\Generate $builderHelper
	)     
	{
		$this->customerSession = $customerSession;
		$this->_urlBuilder = $urlContext->getUrlBuilder();
		$this->builderHelper = $builderHelper;
		$this->cacheManager = $cacheManager;
		$this->_filesystem = $filesystem;
		parent::__construct($context);
	}
	
	public function getModel($model){
		return $this->_objectManager->create($model);
	}
	
    public function execute()
    {
		if(($this->customerSession->getUseFrontendBuilder() == 1) 
			&& ($blockId = $this->customerSession->getBlockCopied()) 
			&& ($pageId = $this->getRequest()->getParam('page_id'))
			&& ($blockName = $this->getRequest()->getParam('block_name'))
		){
			$copyBlock = $this->getModel('MGS\Fbuilder\Model\Child')->load($blockId);
			$copyData = $copyBlock->getData();
			
			unset($copyData['child_id'], $copyData['store_id']);
			$copyData['page_id'] = $pageId;
			$copyData['block_name'] = $blockName;
			
			if($copyData['type']=='modal_popup'){
				$settings = json_decode($copyData['setting'], true);
				$generateBlockId = $settings['generate_block_id'];
				$newGenerateBlockId = rand() . time();
				
				$copyData['setting'] = str_replace($generateBlockId, $newGenerateBlockId, $copyData['setting']);
				$copyData['block_content'] = str_replace($generateBlockId, $newGenerateBlockId, $copyData['block_content']);
				$copyData['custom_style'] = str_replace($generateBlockId, $newGenerateBlockId, $copyData['custom_style']);
			}
			
			$newBlock = $this->getModel('MGS\Fbuilder\Model\Child')->setData($copyData)->save();
			$customStyle = $newBlock->getCustomStyle();
			$customStyle = str_replace('.block'.$blockId,'.block'.$newBlock->getId(),$customStyle);
			$newBlock->setCustomStyle($customStyle)->save();
			
			$this->generateBlockCss();
			
			$this->cacheManager->clean(['full_page']);
			$this->messageManager->addSuccess(__('You duplicated the block.'));
		}
		$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
		$resultRedirect->setUrl($this->_redirect->getRefererUrl());
		return $resultRedirect;
    }
	
	public function generateBlockCss(){
		$model = $this->getModel('MGS\Fbuilder\Model\Child');
		$collection = $model->getCollection();
		$customStyle = '';
		foreach($collection as $child){
			if($child->getCustomStyle() != ''){
				$customStyle .= $child->getCustomStyle();
			}
		}
		if($customStyle!=''){
			try{
				$this->builderHelper->generateFile($customStyle, 'blocks.css', $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('mgs/fbuilder/css/'));
			}catch (\Exception $e) {
				
			}
		}
	}
}
