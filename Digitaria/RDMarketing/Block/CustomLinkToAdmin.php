<?php
namespace Digitaria\RDMarketing\Block;

use Magento\Backend\Model\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class CustomLinkToAdmin extends Template
{
    protected $backendUrl;

    public function __construct(
        Context $context,
        UrlInterface $backendUrl,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->backendUrl = $backendUrl;
    }

    public function getCustomAdminUrl()
    {
        $adminUrl = $this->backendUrl->getUrl('adminhtml/system_config/edit/section/rdmarketing_api/');
        return $adminUrl;
    }
}
