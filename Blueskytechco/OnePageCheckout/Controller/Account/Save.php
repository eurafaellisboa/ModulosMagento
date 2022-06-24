<?php
namespace Blueskytechco\OnePageCheckout\Controller\Account;

class Save extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Checkout\Helper\Cart
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * Save constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        parent::__construct($context);
        $this->checkoutSession = $checkoutSession;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        try {
            $params = $this->getRequest()->getParams();
            if (isset($params['email'])
                && isset($params['pass'])
                && isset($params['confirmpass'])
                && $params['pass'] == $params['confirmpass']
            ) {
                $this->checkoutSession->setNewAccountInformaton(
                    ['email' => $params['email'], 'pass' => $params['pass']]
                );
            } else {
                $this->checkoutSession->unsNewAccountInformaton();
            }
        } catch (\Exception $e) {
            return $result->setData($e->getMessage());
        }
        return $result->setData('done');
    }
}
