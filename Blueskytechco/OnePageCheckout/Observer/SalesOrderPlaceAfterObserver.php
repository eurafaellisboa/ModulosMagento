<?php
namespace Blueskytechco\OnePageCheckout\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class SalesOrderPlaceAfterObserver implements ObserverInterface
{

    /**
     * CUSTOMER_IS_GUEST
     */
    const CUSTOMER_IS_GUEST = 0;

    /**
     * @var GuestToCustomer\Helper\Observer\Helper
     */
    protected $customerFactory;

    /**
     * @var \Magento\Customer\Model\AddressFactory
     */
    protected $addressFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Blueskytechco\OnePageCheckout\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Sales\Model\Order\Status\HistoryFactory
     */
    protected $historyFactory;

    /**
     * @var \Magento\Newsletter\Model\Subscriber
     */
    protected $subscriber;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    public function __construct(
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Blueskytechco\OnePageCheckout\Helper\Data $helper,
        \Magento\Sales\Model\Order\Status\HistoryFactory $historyFactory,
        \Magento\Newsletter\Model\Subscriber $subscriber,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->customerFactory = $customerFactory;
        $this->addressFactory = $addressFactory;
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
        $this->checkoutSession = $checkoutSession;
        $this->helper = $helper;
        $this->historyFactory = $historyFactory;
        $this->subscriber = $subscriber;
        $this->logger = $logger;
    }

    public function execute(EventObserver $observer)
    {
        $additionalData = $this->checkoutSession->getBlueskytechcoOpcAdditionalData();
        $this->checkoutSession->unsBlueskytechcoOpcAdditionalData();
        if (isset($additionalData['order_comment']) && $additionalData['order_comment'] != '') {
            $this->checkoutSession->setBlueskytechcoOpcOrderComment($additionalData['order_comment']);
        }
        $order = $observer->getEvent()->getOrder();
        $websiteId = $this->storeManager->getStore()->getWebsiteId();
        $storeId = $this->storeManager->getStore()->getId();
        if (!$this->helper->getModuleStatus()) {
            $this->subscriber($order, $additionalData, 0);
            return;
        }

        $billingAddress = $order->getBillingAddress();
        $shippingAddress = $order->getShippingAddress();
        $emailGuest = $billingAddress->getData('email');
        $customer = $this->checkEmailCustomer($emailGuest, $websiteId);
        if ($order->getCustomerIsGuest() == 1 && $customer) {
            $observer->getEvent()->getOrder()->setCustomerId(
                $customer->getId()
            )->setCustomerGroupId(
                (int)$customer->getGroupId()
            )->setCustomerIsGuest(
                self::CUSTOMER_IS_GUEST
            )->setCustomerFirstname(
                $customer->getName()
            );
            $customerAttr = $this->checkoutSession->getCustomerAttributes();
            if ($customerAttr && !empty($customerAttr)) {
                $this->checkoutSession->unsCustomerAttributes();
                $customerData = $customer->getDataModel();
                foreach ($customerAttr as $attr => $value) {
                    if (is_array($value)) {
                        $value = implode(',', $value);
                    }
                    $customerData->setCustomAttribute($attr, $value);
                }
                $customer->updateData($customerData);
                try {
                    $customer->save();
                } catch (\Exception $e) {
                    $this->logger->debug($e->getMessage());
                }
            }
            $this->subscriber($order, $additionalData, $customer->getId());
            return $this;
        }
        $pass = $this->isCreateNewAccount($emailGuest);
        if (!$pass) {
            if ($order->getCustomerIsGuest() == 0) {
                $this->subscriber($order, $additionalData, $order->getCustomerId());
            } else {
                $this->subscriber($order, $additionalData, 0);
            }
            return;
        }
        if ($order->getCustomerIsGuest() == 1 && !$customer) {
            try {
                $shippingAddress = $shippingAddress ? $shippingAddress->getData() : [];
                $shippingAddress = $this->removeExtensionAttributes($shippingAddress);
                $billingAddress = $billingAddress->getData();
                $defaultCustomerGroupId = $this->helper->getDefaultCustomerGroupId($storeId);
                $customer = $this->createNewAccount(
                    $billingAddress,
                    $websiteId,
                    $storeId,
                    $defaultCustomerGroupId,
                    $pass
                );
                if (!$customer) {
                    $this->subscriber($order, $additionalData, 0);
                    return;
                }
                $customerId = $customer->getId();
                $customerAddressId = $this->saveCustomerAddresses($customerId, $shippingAddress, $billingAddress);
                if (empty($customerAddressId)) {
                    $this->subscriber($order, $additionalData, $customer->getId());
                    return;
                }
                $observer->getEvent()->getOrder()
                    ->setCustomerId($customerId)
                    ->setCustomerEmail($billingAddress['email'])
                    ->setCustomerFirstname($billingAddress['firstname'])
                    ->setCustomerLastname($billingAddress['lastname'])
                    ->setCustomerIsGuest(self::CUSTOMER_IS_GUEST)
                    ->setCustomerGroupId($defaultCustomerGroupId);

                //Set order address
                $this->setOrderAddress($customerId, $customerAddressId, $observer);
                $this->customerSession->setCustomerAsLoggedIn($customer);
            } catch (\Exception $e) {
                $this->logger->debug($e->getMessage());
            }
        }
        if ($order->getCustomerIsGuest() == 0) {
            $this->subscriber($order, $additionalData, $order->getCustomerId());
        } else {
            $this->subscriber($order, $additionalData, 0);
        }

    }
    /**
     * Check Create Account
     *
     * @param string $emailGuest
     * @return bool
     */
    protected function isCreateNewAccount($emailGuest)
    {
        $newCustomer = $this->checkoutSession->getNewAccountInformaton();
        if ($newCustomer &&
            isset($newCustomer['email']) &&
            isset($newCustomer['pass']) &&
            $emailGuest == $newCustomer['email']
        ) {
            $this->checkoutSession->unsNewAccountInformaton();
            return $newCustomer['pass'];
        }
        return false;
    }

    /**
     * Check exist Customer Email
     *
     * @param string $customerEmail
     * @param int $websiteId
     * @return bool|\Magento\Customer\Model\Customer
     */
    protected function checkEmailCustomer($customerEmail, $websiteId)
    {
        $customer = $this->customerFactory->create()->setWebsiteId($websiteId)->loadByEmail($customerEmail);
        if ($customer->getId()) {
            return $customer;
        } else {
            return false;
        }
    }

    /**
     * Remove Attribute
     *
     * @param array $shippingAddress
     * @return mixed
     */
    protected function removeExtensionAttributes($shippingAddress)
    {
        if (isset($shippingAddress['extension_attributes'])) {
            unset($shippingAddress['extension_attributes']);
        }
        return $shippingAddress;
    }

    /**
     * Create New Account
     *
     * @param array $billingAddress
     * @param int $websiteId
     * @param int $storeId
     * @param int $defaultCustomerGroupId
     * @param string $pass
     * @return bool|\Magento\Customer\Model\Customer
     */
    protected function createNewAccount($billingAddress, $websiteId, $storeId, $defaultCustomerGroupId, $pass)
    {
        $customer = $this->customerFactory->create();
        $customer->setWebsiteId($websiteId);
        $customer->setEmail($billingAddress['email']);
        $customer->setGroupId($defaultCustomerGroupId);
        $customer->setFirstname($billingAddress['firstname']);
        $customer->setLastname($billingAddress['lastname']);
        if (isset($billingAddress['prefix']) && $billingAddress['prefix'] != null) {
            $customer->setPrefix($billingAddress['prefix']);
        }
        if (isset($billingAddress['suffix']) && $billingAddress['suffix'] != null) {
            $customer->setPrefix($billingAddress['suffix']);
        }
        $customer->setPassword($pass);
        $customerAttr = $this->checkoutSession->getCustomerAttributes();
        if ($customerAttr && !empty($customerAttr)) {
            $this->checkoutSession->unsCustomerAttributes();
            $customerData = $customer->getDataModel();
            foreach ($customerAttr as $attr => $value) {
                if (is_array($value)) {
                    $value = implode(',', $value);
                }
                $customerData->setCustomAttribute($attr, $value);
            }
            $customer->updateData($customerData);
        }
        try {
            $customer->save();
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
            return false;
        }
        try {
            $customer->reindex();
            $customer->sendNewAccountEmail('registered', '', $storeId);
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }

        return $customer;
    }

    /**
     * Save Customer Address
     *
     * @param int $customerId
     * @param array $shippingAddress
     * @param array $billingAddress
     * @return array
     * @throws \Exception
     */
    protected function saveCustomerAddresses($customerId, $shippingAddress, $billingAddress)
    {
        $result = [];
        if (!$this->isAsyncAddress($shippingAddress, $billingAddress)) {
            if (!empty($shippingAddress)) {
                $shipping = $this->saveAddressCustomer(
                    $shippingAddress,
                    $customerId,
                    'shipping'
                );
                if ($shipping) {
                    $result['billing'] = $shipping;
                }
            }
            if (!empty($billingAddress)) {
                $billing = $this->saveAddressCustomer(
                    $billingAddress,
                    $customerId,
                    'billing'
                );
                if ($billing) {
                    $result['billing'] = $billing;
                }
            }
        } else {
            if (isset($shippingAddress['address_type'])) {
                if (!empty($shippingAddress)) {
                    $both = $this->saveAddressCustomer(
                        $shippingAddress,
                        $customerId,
                        'both'
                    );
                    if ($both) {
                        $result['billing_shipping'] = $both;
                    }

                }
            }
        }
        return $result;
    }

    /**
     * Check Shipping same Billing Address
     *
     * @param array $shippingAddress
     * @param array $billingAddress
     * @return bool
     */
    protected function isAsyncAddress($shippingAddress, $billingAddress)
    {
        if (empty($shippingAddress) || empty($billingAddress)) {
            return false;
        }
        unset($shippingAddress['address_type']);
        unset($billingAddress['address_type']);
        unset($shippingAddress['quote_address_id']);
        unset($billingAddress['quote_address_id']);
        unset($shippingAddress['id']);
        unset($billingAddress['id']);
        unset($shippingAddress['entity_id']);
        unset($billingAddress['entity_id']);
        if (!empty(array_diff($shippingAddress, $billingAddress))) {
            $sameAddress = false;
        } else {
            $sameAddress = true;
        }
        return $sameAddress;
    }

    /**
     * Save Address to Customer
     *
     * @param array $addressData
     * @param int $customerId
     * @param string $type
     * @return bool|mixed
     */
    protected function saveAddressCustomer($addressData, $customerId, $type)
    {
        $customerAddress = $this->addressFactory->create();
        $customerAddress->setCustomerId($customerId)
            ->setFirstname($addressData['firstname'])
            ->setLastname($addressData['lastname'])
            ->setCountryId($addressData['country_id'])
            ->setRegionId($addressData['region_id'])
            ->setRegion($addressData['region'])
            ->setPostcode($addressData['postcode'])
            ->setCity($addressData['city'])
            ->setTelephone($addressData['telephone'])
            ->setCompany($addressData['company'])
            ->setStreet($addressData['street'])
            ->setSaveInAddressBook('1');

        if ($type == 'billing') {
            $customerAddress->setIsDefaultBilling('1');
        } elseif ($type == 'shipping') {
            $customerAddress->setIsDefaultShipping('1');
        } else {
            $customerAddress->setIsDefaultBilling('1');
            $customerAddress->setIsDefaultShipping('1');
        }
        try {
            $customerAddress->save();
            return $customerAddress->getId();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Save Address to Order
     *
     * @param int $customerId
     * @param array $customerAddress
     * @param EventObserver $observer
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function setOrderAddress($customerId, $customerAddress, $observer)
    {
        $idDefaultBilling = false;
        $idDefaultShipping = false;
        if (isset($customerAddress['billing_shipping'])) {
            $idDefaultBilling = $idDefaultShipping = $customerAddress['billing_shipping'];
        } else {
            if (isset($customerAddress['billing'])) {
                $idDefaultBilling = $customerAddress['billing'];
            }
            if (isset($customerAddress['shipping'])) {
                $idDefaultShipping = $customerAddress['shipping'];
            }
            if ($idDefaultBilling && !$idDefaultShipping) {
                $idDefaultShipping = $idDefaultBilling;
            }
            if (!$idDefaultBilling && $idDefaultShipping) {
                $idDefaultBilling = $idDefaultShipping;
            }
        }
        $order = $observer->getEvent()->getOrder();
        if ($order->getBillingAddress()) {
            $order->getBillingAddress()->setCustomerId($customerId);
            $order->getBillingAddress()->setCustomerAddressId($idDefaultBilling);
        }
        if ($order->getShippingAddress()) {
            $order->getShippingAddress()->setCustomerId($customerId);
            $order->getShippingAddress()->setCustomerAddressId($idDefaultShipping);
        }
    }

    /**
     * Subscriber
     *
     * @param Magento\Sales\Model\Order $order
     * @param array $additionalData
     * @param int $customerId
     * @throws \Exception
     */
    protected function subscriber($order, $additionalData, $customerId)
    {
        if (!empty($additionalData)
            && $this->helper->isDisplayField('show_subscribe_newsletter')
        ) {
            if ($customerId != 0 && $customerId != null) {
                $subscriberModel = $this->subscriber->loadByCustomerId($customerId);
                if (!$subscriberModel->isSubscribed()) {
                    try {
                        $this->subscriber->subscribeCustomerById($customerId);
                    } catch (\Exception $e) {
                        $this->logger->error($e->getMessage());
                    }
                }
            } else {
                $subscriberModel = $this->subscriber->loadByEmail($order->getCustomerEmail());
                if (!$subscriberModel->isSubscribed()) {
                    try {
                        $this->subscriber->subscribe($order->getCustomerEmail());
                    } catch (\Exception $e) {
                        $this->logger->error($e->getMessage());
                    }
                }
            }
        }
    }
}
