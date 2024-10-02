<?php
namespace Digitaria\RDMarketing\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Request\Http;
use Magento\Backend\Block\Template\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;

class Link extends Field
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var Http
     */
    private $request;

    /**
     * @var string|null
     */
    private $clientId;

    /**
     * @var Context
     */
    private $context;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param Http $request
     * @param StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        Http $request,
        StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->request = $request;
        $this->clientId = $this->scopeConfig->getValue('rdmarketing_api/authentication/client_id');
        $this->context = $context;
        $this->storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    /**
     * Render link
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
		 
		 $frontendUrl = $this->scopeConfig->getValue('web/secure/base_url');
         $redirectUri = rtrim($frontendUrl, '/') . '/rdmarketing/index/index/';
         $customUrls = $this->getUrl('rdmarketing/code/index');
	     //$html = '<br><br>'.__('The callback URL that should be registered in the App within RD Station Marketing is:').'<br>';
        //$html .= $redirectUri . '<br><br>';	 
        if (empty($this->clientId)) {
            $html = '<i>'.__('You need to enter a Client ID to generate the authentication URL').'</i>';
            return $html;
        } elseif (!empty($this->scopeConfig->getValue('rdmarketing_api/authentication/code'))) {
            $html = '<i>'.__('The Code has already been generated. To generate the link again, remove the saved code above.').'</i>';
            return $html;
        }

        //$url = 'https://api.rd.services/auth/dialog'; // Define the correct route to your controller

$html = '<a class="action-primary" href="' . $customUrls . '" target="_blank">';
$html .= __('Generate Code');
$html .= '</a>';

return $html;

    }
}