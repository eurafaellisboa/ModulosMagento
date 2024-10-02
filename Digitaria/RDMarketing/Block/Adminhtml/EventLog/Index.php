<?php
namespace Digitaria\RDMarketing\Block\Adminhtml\EventLog;

use Magento\Backend\Block\Template;
use Digitaria\RDMarketing\Model\ResourceModel\EventLog\Grid\CollectionFactory as EventLogCollectionFactory;

class Index extends Template
{
    protected $eventLogCollectionFactory;

    public function __construct(
        Template\Context $context,
        EventLogCollectionFactory $eventLogCollectionFactory,
        array $data = []
    ) {
        $this->eventLogCollectionFactory = $eventLogCollectionFactory;
        parent::__construct($context, $data);
    }

    public function getEventLogs()
    {
        $eventLogCollection = $this->eventLogCollectionFactory->create();
        return $eventLogCollection->getItems();
    }
}
