<?php
namespace Digitaria\SearchCustomerEmail\Block;

use Magento\Framework\View\Element\Template;

class FormAction extends Template
{
    public function __construct(
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getFormAction()
    {
        return $this->getUrl('searchcustomeremail/recoveremail/search', ['_secure' => true]);
    }
}
