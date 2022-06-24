<?php
namespace Blueskytechco\AskQuestion\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Store\Model\ScopeInterface;

class Save extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Blueskytechco\AskQuestion\Model\QuestionFactory
     */
    protected $questionfactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productloader;

    /**
     * @var \Blueskytechco\AskQuestion\Helper\Email
     */
    protected $emailSender;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    public function __construct
    (
        Context $context,
        \Blueskytechco\AskQuestion\Model\QuestionFactory $questionfactory,
        \Magento\Catalog\Model\ProductFactory $_productloader,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Controller\Result\JsonFactory $resultPageFactory,
        \Blueskytechco\AskQuestion\Helper\Email $emailSender
    )
    {
        parent::__construct($context);
        $this->questionfactory = $questionfactory;
        $this->_productloader = $_productloader;
        $this->scopeConfig = $scopeConfig;
        $this->resultPageFactory = $resultPageFactory;
        $this->emailSender = $emailSender;
    }
    public function execute()
    {
        $receiveEmail = $this->scopeConfig->getValue('question_email/general/email_received', ScopeInterface::SCOPE_STORE);
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $post = $this->getRequest()->getParams();
        if (!empty($post)) {
            if (
                !$post['customer_name'] || !$post['email'] || 
                !$post['phone'] || !$post['message']
            ) {
                $message = __(
                    'Error send question!'
                );
                $this->messageManager->addErrorMessage($message);
                $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                return $resultRedirect;
            }
            $model = $this->questionfactory->create();
            $model->setData($post)->save();
        }
        $product = $this->_productloader->create()->load($post['product_id']);
        $product_name = $product->getName();
        $emailTemplateData = [
            'customer_name' => $post['customer_name'],
            'email' => $post['email'],
            'phone' => $post['phone'],
            'product' => $product_name,
            'message' => $post['message']
        ];
        $this->emailSender->sendEmail($receiveEmail, $emailTemplateData);
        $message = __(
            'Thank you for send us question!'
        );
        $this->messageManager->addSuccessMessage($message);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }
}