<?php
namespace Digitaria\RDMarketing\Block\Adminhtml\Code;

use Magento\Framework\View\Element\Template;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Backend\Helper\Data as BackendHelper;

class Index extends Template
{
    /**
     * @var BackendHelper
     */
    private $backendHelper;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param Context $context
     * @param BackendHelper $backendHelper
     * @param ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        BackendHelper $backendHelper,
        ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        $this->backendHelper = $backendHelper;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve the admin base URL
     *
     * @return string
     */
    public function getAdminBaseUrl()
    {
        return $this->backendHelper->getHomePageUrl();
    }

    /**
     * Retrieve the code value
     *
     * @return string|null
     */
    public function getCode()
    {
        return $this->scopeConfig->getValue('rdmarketing_api/authentication/code');
    }

    public function getClientID()
    {
        return $this->scopeConfig->getValue('rdmarketing_api/authentication/client_id');
    }

    public function getClientSecret()
    {
        return $this->scopeConfig->getValue('rdmarketing_api/authentication/client_secret');
    }

    public function getSecureBaseUrl()
    {
        return $this->scopeConfig->getValue('web/secure/base_url');
    }
}
