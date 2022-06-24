<?php
namespace Blueskytechco\OnePageCheckout\Block\Adminhtml\Field;

use Magento\Backend\Block\Widget\Container;
use Magento\Backend\Block\Widget\Context;

class Tabs extends Container
{
    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        parent::_construct();

        $this->addButton('save', [
            'label' => __('Save Position'),
            'class' => 'save primary admin-save-position',
        ]);
    }

    /**
     * Retrieve the header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        return (string) __('Manage Fields');
    }

    /**
     * @return string
     */
    public function getAjaxUrl()
    {
        return $this->getUrl('*/*/save');
    }
}
