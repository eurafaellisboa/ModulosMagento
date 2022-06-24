<?php
namespace Blueskytechco\SizeChart\Model\ResourceModel\SizeChart;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Blueskytechco\SizeChart\Model\SizeChart::class,
            \Blueskytechco\SizeChart\Model\ResourceModel\SizeChart::class
        );
    }
}
