<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MGS\ThemeSettings\Controller\Adminhtml\ThemeSettings;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
class Importstatic extends \MGS\ThemeSettings\Controller\Adminhtml\ThemeSettings
{
	protected $_filesystem;
	
	/**
	 * @var \Magento\Framework\Xml\Parser
	 */
	private $_parser;
	
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
		\Magento\Backend\App\Action\Context $context,
		\Magento\Framework\Xml\Parser $parser,
		\Magento\Framework\Filesystem $filesystem
	)
    {
        parent::__construct($context);
		$this->_filesystem = $filesystem;
		$this->_parser = $parser;
    }

    /**
     * Edit sitemap
     *
     * @return void
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
		if($theme = $this->getRequest()->getParam('theme')){
			$filePath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('mgs/static_blocks/'.$theme.'.xml');
			try {
				if (is_readable($filePath)){
					$parsedArray = $this->_parser->load($filePath)->xmlToArray();
					if(isset($parsedArray['static_block']['item']) && (count($parsedArray['static_block']['item'])>0)){
						foreach($parsedArray['static_block']['item'] as $staticBlock){
							if(is_array($staticBlock)){
								$identifier = $staticBlock['identifier'];
								$staticBlockData = $staticBlock;
							}else{
								$identifier = $parsedArray['static_block']['item']['identifier'];
								$staticBlockData = $parsedArray['static_block']['item'];
							}
							
							$staticBlocksCollection = $this->_objectManager->create('Magento\Cms\Model\Block')
								->getCollection()
								->addFieldToFilter('identifier', $identifier)
								->load();
							if (count($staticBlocksCollection) > 0){
								foreach ($staticBlocksCollection as $_item){
									$_item->delete();
								}
							}
							
							$this->_objectManager->create('Magento\Cms\Model\Block')->setData($staticBlockData)->setIsActive(1)->setStores(array(0))->save();
							
						}
						$this->messageManager->addSuccess(__('Static blocks was successfully imported.'));
					}else{
						$this->messageManager->addError(__('The file is corrupted!'));
					}
				}
			}catch (\Exception $e) {
				// display error message
				$this->messageManager->addError($e->getMessage());
			}
		}else{
			$this->messageManager->addError(__('The file to import no longer exists.'));
		}
		$this->_redirect($this->_redirect->getRefererUrl());
		return;
    }
}
