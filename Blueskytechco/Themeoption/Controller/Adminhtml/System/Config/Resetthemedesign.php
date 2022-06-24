<?php

namespace Blueskytechco\Themeoption\Controller\Adminhtml\System\Config;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\PageCache\Version;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Cache\Frontend\Pool;

class Resetthemedesign extends Action
{

    protected $resultJsonFactory;
	protected $cacheTypeList;
	protected $cacheFrontendPool;

    public function __construct(
        Context $context,
		TypeListInterface $cacheTypeList, 
		Pool $cacheFrontendPool,
        JsonFactory $resultJsonFactory
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;
		$this->cacheTypeList = $cacheTypeList;
		$this->cacheFrontendPool = $cacheFrontendPool;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection(); 
		$core_config_data = $resource->getTableName('core_config_data');
		$sql = "Delete FROM " . $core_config_data." WHERE `path` LIKE '%themeoption%'"; 
		$connection->query($sql);
		
		$_types = [
            'config',
            'layout',
            'block_html',
            'full_page'
        ];
 
		foreach ($_types as $type) {
			$this->cacheTypeList->cleanType($type);
		}
		
		foreach ($this->cacheFrontendPool as $cacheFrontend) {
			$cacheFrontend->getBackend()->clean();
		}
		
        return $result->setData(['success' => true]);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Blueskytechco_Themeoption::resetthemeoption');
    }
}
?>