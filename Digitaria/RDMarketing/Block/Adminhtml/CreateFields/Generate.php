<?php 
namespace Digitaria\RDMarketing\Block\Adminhtml\CreateFields;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Digitaria\RDMarketing\Service\Connect;
use Magento\Framework\HTTP\Client\Curl;

class Generate extends Template
{
    protected $connect;
    protected $pageFactory;
    protected $curl;

    public function __construct(
        Context $context,
        Connect $connect,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        Curl $curl,
        array $data = []
    ) {
        $this->connect = $connect;
        $this->pageFactory = $pageFactory;
        $this->curl = $curl;
        parent::__construct($context, $data);
    }

    public function execute()
{
    $code = $this->scopeConfig->getValue('rdmarketing_api/authentication/code');

    if (!$code) {
        $clientId = $this->scopeConfig->getValue('rdmarketing_api/authentication/client_id');
        $redirectUri = $this->_url->getUrl('admin/rdmarketing/code/index');

        $url = $this->_url->getUrl(
            'https://api.rd.services/auth/dialog',
            [
                'client_id' => $clientId,
                'redirect_uri' => $redirectUri,
                'state' => '',
            ]
        );

        return $this->getResponse()->setRedirect($url);
    } else {
        //$html = '<p>O Code já foi gerado. Para gerar o link novamente, remova o code salvo aqui.</p>';
        //return $html;
    }

    $this->_view->loadLayout();
    $this->_view->getPage()->getConfig()->getTitle()->prepend(__('RD Station Code'));
    $this->_view->renderLayout();
}
}
