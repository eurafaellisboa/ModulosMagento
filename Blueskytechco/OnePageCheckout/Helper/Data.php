<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Blueskytechco\OnePageCheckout\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\GiftMessage\Helper\Message;
use Magento\Framework\ObjectManagerInterface;
/**
 * Catalog category helper
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Data extends AbstractHelper
{
    const XML_PATH_STATUS_ONEPAGECHECKOUT = 'onepagecheckout/general/enabled';
    const XML_PATH_URL_ONEPAGECHECKOUT = 'onepagecheckout/general/seourl';
    const GENERAL_GROUP = 'onepagecheckout/general/';
    const XML_PATH_SORTED_FIELD_POSITION = 'onepagecheckout/field/position';

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    protected $objectManager;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        ObjectManagerInterface $objectManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
        $this->objectManager = $objectManager;
        parent::__construct($context);
    }
    public function getConfigUrl()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_URL_ONEPAGECHECKOUT);
    }
    public function getModuleStatus()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_STATUS_ONEPAGECHECKOUT);
    }
    public function getGeneral($field, $storeId = null)
    {
        if (!$this->getModuleStatus()) {
            return false;
        }
        return $this->scopeConfig->getValue(
            self::GENERAL_GROUP . $field,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
    /**
     * @param string $field
     * @param null|int $storeId
     * @return mixed
     */
    public function isDisplayField($field, $storeId = null)
    {
        if (!$this->getModuleStatus()) {
            return false;
        }
        return $this->scopeConfig->isSetFlag(
            self::GENERAL_GROUP . $field,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
    public function getPaymentOnlineMethods()
    {
        $onlineMethodList = [
            'payflowpro',
            'payflow_link',
            'payflow_advanced',
            'braintree_paypal',
            'paypal_express_bml',
            'payflow_express_bml',
            'payflow_express',
            'paypal_express',
            'authorizenet_directpost',
            'realexpayments_hpp',
            'braintree'
        ];
        return $onlineMethodList;
    }
    public function isMessagesAllowed($store = null)
    {
        if (!$this->getModuleStatus()) {
            return false;
        }
        return $this->scopeConfig->isSetFlag(
            Message::XPATH_CONFIG_GIFT_MESSAGE_ALLOW_ORDER,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
    public function getDefaultCustomerGroupId($storeId = null)
    {
        return $this->scopeConfig->getValue(
            'customer/create_account/default_group',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
    public function getConfigValue($field, $scopeValue = null, $scopeType = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue($field, $scopeType, $scopeValue);
    }
    /**
     * @return bool
     */
    public function isEnableAmazonPay()
    {
        return $this->isModuleOutputEnabled('Amazon_Payment');
    }/**
 * @param $path
 *
 * @return mixed
 */
    public function getObject($path)
    {
        return $this->objectManager->get($path);
    }
}
