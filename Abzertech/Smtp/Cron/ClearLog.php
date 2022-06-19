<?php

namespace Abzertech\Smtp\Cron;

use Abzertech\Smtp\Helper\Data;
use Abzertech\Smtp\Model\CoreFactory;

class ClearLog
{
    
    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     *
     * @var CoreFactory
     */
    protected $coreFactory;
    
    /**
     *
     * @param CoreFactory $coreFactory
     */
    public function __construct(
        Data $dataHelper,
        CoreFactory $coreFactory
    ) {
        $this->dataHelper = $dataHelper;
        $this->coreFactory = $coreFactory;
    }

    /**
     * Default execute function.
     *
     * @return null
     */
    public function execute()
    {
        try {
            $model = $this->coreFactory->create();
            $connection = $model->getCollection()->getConnection();
            $tableName = $model->getCollection()->getMainTable();
            $clearLog = $this->dataHelper->getClearLog();
            
            if ($clearLog !== 'never') {
                switch ($clearLog) {
                    case 'daily':
                        $connection->truncateTable($tableName);
                        break;
                    case 'weekly':
                        if ($this->isWeekend()) {
                            $connection->truncateTable($tableName);
                        }
                        break;
                    case 'monthly':
                        if ($this->isMonthStart()) {
                            $connection->truncateTable($tableName);
                        }
                        break;
                    case 'yearly':
                        if ($this->isNewYear()) {
                            $connection->truncateTable($tableName);
                        }
                        break;
                }
            }
            
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }
    /**
     *
     * @return boolean
     */
    private function isWeekend()
    {
        return (date('N', time()) >= 6);
    }
    /**
     *
     * @return boolean
     */
    private function isMonthStart()
    {
        return (date('j', time()) === '1');
    }
    /**
     *
     * @return boolean
     */
    private function isNewYear()
    {
        return (date('z', time()) === '0');
    }
}
