<?php
/**
 * Copyright © 2022 Blueskytechco. All rights reserved.
 */
namespace Blueskytechco\AskQuestion\Model\ResourceModel\Question;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'question_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Blueskytechco\AskQuestion\Model\Question', 'Blueskytechco\AskQuestion\Model\ResourceModel\Question');
    }

}