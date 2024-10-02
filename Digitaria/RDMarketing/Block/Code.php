<?php
namespace Digitaria\RDMarketing\Block;

use Magento\Framework\View\Element\Template;
use Magento\Backend\Model\UrlInterface;


class Code extends Template
{
    /**
     * Retrieve the code value
     *
     * @return string|null
     */
    
    public function getCode()
    {
        return $this->getRequest()->getParam('code');
    }
}
