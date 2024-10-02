<?php

namespace Digitaria\CustomSubscribe\Controller\Subscriber;

use Magento\Customer\Api\AccountManagementInterface as CustomerAccountManagement;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Newsletter\Model\Subscriber;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Exception;

/**
 * New newsletter subscription action
 */
class NewAction extends Action
{
    /**
     * @var SubscriberFactory
     */
    private $subscriberFactory;

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CustomerUrl
     */
    private $customerUrl;

    /**
     * @var CustomerAccountManagement
     */
    private $customerAccountManagement;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Initialize dependencies.
     *
     * @param Context $context
     * @param SubscriberFactory $subscriberFactory
     * @param Session $customerSession
     * @param StoreManagerInterface $storeManager
     * @param CustomerUrl $customerUrl
     * @param CustomerAccountManagement $customerAccountManagement
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        SubscriberFactory $subscriberFactory,
        Session $customerSession,
        StoreManagerInterface $storeManager,
        CustomerUrl $customerUrl,
        CustomerAccountManagement $customerAccountManagement,
        LoggerInterface $logger
    ) {
        $this->subscriberFactory = $subscriberFactory;
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
        $this->customerUrl = $customerUrl;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * New subscription action
     *
     * @return Redirect
     */
    public function execute()
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/subscribe.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        if ($this->getRequest()->isPost() && $this->getRequest()->getPost('email')) {
            $email = (string) $this->getRequest()->getPost('email');
            $name = (string) $this->getRequest()->getPost('subscribe_name');

            $logger->info('Subscriber Name: ' . $name);
            $logger->info('Subscriber Email: ' . $email);

            try {
                $this->validateEmailFormat($email);
                $this->validateGuestSubscription();

                $websiteId = (int) $this->storeManager->getStore()->getWebsiteId();
                /** @var Subscriber $subscriber */
                $subscriber = $this->subscriberFactory->create()->loadByEmail($email);
                if ($subscriber->getId() && (int) $subscriber->getSubscriberStatus() === Subscriber::STATUS_SUBSCRIBED) {
                    throw new LocalizedException(__('This email address is already subscribed.'));
                }

                if (!$subscriber->getId()) {
                    $subscriber->setSubscriberEmail($email);
                    $subscriber->setSubscriberStatus(Subscriber::STATUS_SUBSCRIBED);
                    $subscriber->setStoreId($this->storeManager->getStore()->getId());
                    $subscriber->setData('subscribe_name', $name);
                    $logger->info('Passou pelo salvamento do Nome: ');
                    $subscriber->save();

                    $message = $this->getSuccessMessage((int) $subscriber->getSubscriberStatus());
                    $logger->info($message);
                    $this->messageManager->addSuccessMessage($message);
                }
            } catch (LocalizedException $e) {
                $this->messageManager->addComplexErrorMessage('localizedSubscriptionErrorMessage', ['message' => $e->getMessage()]);
                $logger->err('Erro1: ' . $e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong with the subscription.'));
                $logger->err('Erro2: ' . $e->getMessage());
            }
        }

        /** @var Redirect $redirect */
        $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $redirectUrl = $this->_redirect->getRedirectUrl();
        return $redirect->setUrl($redirectUrl);
    }

    /**
     * Get customer id from session if he is owner of the email
     *
     * @param string $email
     * @return int|null
     */
    private function getSessionCustomerId(string $email): ?int
    {
        if (!$this->customerSession->isLoggedIn()) {
            return null;
        }

        $customer = $this->customerSession->getCustomerDataObject();
        if ($customer->getEmail() !== $email) {
            return null;
        }

        return (int) $this->customerSession->getId();
    }

    /**
     * Get success message
     *
     * @param int $status
     * @return Phrase
     */
    private function getSuccessMessage(int $status): Phrase
    {
        if ($status === Subscriber::STATUS_NOT_ACTIVE) {
            return __('The confirmation request has been sent.');
        }

        return __('Thank you for your subscription.');
    }

    /**
     * Validate the email format
     *
     * @param string $email
     * @throws LocalizedException
     */
    private function validateEmailFormat(string $email)
    {
        if (!\Zend_Validate::is($email, \Magento\Framework\Validator\EmailAddress::class)) {
            throw new LocalizedException(__('Please enter a valid email address.'));
        }
    }

    /**
     * Validate if the guest can subscribe
     *
     * @throws LocalizedException
     */
    private function validateGuestSubscription()
    {
        if ($this->customerSession->isLoggedIn() && !$this->customerAccountManagement->isReadonly()) {
            throw new LocalizedException(__('You cannot subscribe to the newsletter using guest checkout.'));
        }
    }
}
