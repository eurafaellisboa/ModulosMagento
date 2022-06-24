<?php
namespace Blueskytechco\OnePageCheckout\Block\Adminhtml\Field;

/**
 * Class Address
 * @package Blueskytechco\OnePageCheckout\Block\Adminhtml\Field
 */
class Address extends AbstractField
{
    const BLOCK_ID = 'admin-address-information';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        parent::_construct();

        /** Prepare collection */
        list($this->sortedFields, $this->availableFields) = $this->helper->getSortedField(false);
    }

    /**
     * @return string
     */
    public function getBlockTitle()
    {
        return (string) __('Address Information');
    }
}
