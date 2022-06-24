<?php
namespace Blueskytechco\AskQuestion\Model;

use Magento\Framework\Model\AbstractModel;
use \Magento\Framework\DataObject\IdentityInterface;
use Blueskytechco\AskQuestion\Model\ResourceModel\Question as QuestionResourceModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;

class Question extends AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'ask_question';

    protected $_cacheTag = 'ask_question';

    protected $_eventPrefix = 'ask_question';

    protected function _construct()
    {
        $this->_init('Blueskytechco\AskQuestion\Model\ResourceModel\Question');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
