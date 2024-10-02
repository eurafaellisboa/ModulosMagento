<?php
namespace Digitaria\RDMarketing\Cron;

use Digitaria\RDMarketing\Model\ResourceModel\EventLog\Grid\CollectionFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class CleanLogs
{
    protected $collectionFactory;
    protected $objectManager;
    protected $scopeConfig;

    public function __construct(
        CollectionFactory $collectionFactory,
        ObjectManagerInterface $objectManager,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->objectManager = $objectManager;
        $this->scopeConfig = $scopeConfig;
    }

    public function execute()
    {
		 
		  $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/cleanup.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $cleanupDays = $this->scopeConfig->getValue('rdmarketing_api/log_settings/log_cleanup_days'); // Obter o valor do campo log_cleanup_days
		  $logger->info('apagar em' .$cleanupDays);
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('event_date', ['lteq' => date('Y-m-d H:i:s', strtotime('-' . $cleanupDays . ' days'))]);

        $recordDeleted = 0;
        foreach ($collection as $item) {
            $deleteItem = $this->objectManager->create('Digitaria\RDMarketing\Model\EventLog')->load($item->getId());
            $deleteItem->delete();
            $recordDeleted++;
        }
        return $this;
    }
}
