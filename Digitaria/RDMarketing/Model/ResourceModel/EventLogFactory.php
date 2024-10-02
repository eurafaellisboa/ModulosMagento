<?php
namespace Digitaria\RDMarketing\Model\ResourceModel;

use Magento\Framework\ObjectManagerInterface;

class EventLogFactory
{
    protected $objectManager;
    protected $instanceName;

    public function __construct(
        ObjectManagerInterface $objectManager,
        $instanceName = '\\Digitaria\\RDMarketing\\Model\\ResourceModel\\EventLog'
    ) {
        $this->objectManager = $objectManager;
        $this->instanceName = $instanceName;
    }

    public function create()
    {
        return $this->objectManager->create($this->instanceName);
    }
}
