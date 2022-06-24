<?php

namespace Blueskytechco\Themeoption\Block\Adminhtml\Form\Field;

class CustomTabs extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    protected $_elementFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Data\Form\Element\Factory $elementFactory,
        array $data = []
    ) {
        $this->_elementFactory = $elementFactory;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        $this->addColumn('tabs_type', ['label' => __('Type')]);
        $this->addColumn('tabs_title', ['label' => __('Title')]);
        $this->addColumn('tabs_code', ['label' => __('Identifier')]);
        $this->addColumn('sort_order', ['label' => __('Order')]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Tab');

        parent::_construct();
    }

    public function renderCellTemplate($columnName)
    {
        if ($columnName == 'tabs_type' && isset($this->_columns[$columnName])) {
            $options = [['value' => 'blocks', 'label' => 'Blocks'], ['value' => 'attributes', 'label' => 'Attributes']];
            $element = $this->_elementFactory->create('select');
            $element->setForm(
                $this->getForm()
            )->setName(
                $this->_getCellInputElementName($columnName)
            )->setHtmlId(
                $this->_getCellInputElementId('<%- _id %>', $columnName)
            )->setValues(
                $options
            );
            return str_replace("\n", '', $element->getElementHtml());
        }

        return parent::renderCellTemplate($columnName);
    }
}
