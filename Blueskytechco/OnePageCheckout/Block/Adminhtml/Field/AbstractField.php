<?php
namespace Blueskytechco\OnePageCheckout\Block\Adminhtml\Field;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Widget\Context;
use Magento\Customer\Model\Attribute;
use Blueskytechco\OnePageCheckout\Helper\Address;

/**
 * Class AbstractField
 * @package Blueskytechco\OnePageCheckout\Block\Adminhtml\Field
 */
abstract class AbstractField extends Template
{
    const BLOCK_ID = '';

    /**
     * @var string
     */
    protected $_template = 'Blueskytechco_OnePageCheckout::field/position.phtml';

    /**
     * @var Address
     */
    protected $helper;

    /**
     * @var Attribute[]
     */
    protected $sortedFields = [];

    /**
     * @var Attribute[]
     */
    protected $availableFields = [];

    /**
     * AbstractField constructor.
     *
     * @param Context $context
     * @param Address $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Address $helper,
        array $data = []
    ) {
        $this->helper = $helper;

        parent::__construct($context, $data);
    }

    /**
     * Retrieve the header text
     *
     * @return string
     */
    abstract public function getBlockTitle();

    /**
     * @return string
     */
    public function getBlockId()
    {
        return static::BLOCK_ID;
    }

    /**
     * @return Attribute[]
     */
    public function getSortedFields()
    {
        return $this->sortedFields;
    }

    /**
     * @return Attribute[]
     */
    public function getAvailableFields()
    {
        return $this->availableFields;
    }

    /**
     * @return Address
     */
    public function getHelperData()
    {
        return $this->helper;
    }

    /**
     * @return bool
     */
    public function isVisible()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function hasFields()
    {
        return true;
    }

    /**
     * @return string
     */
    public function getNoticeMessage()
    {
        return '';
    }
}
