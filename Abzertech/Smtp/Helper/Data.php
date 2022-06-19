<?php

namespace Abzertech\Smtp\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{

    /**
     * @var null $storeId
     */
    protected $storeId = null;

    /**
     * @param null $store_id
     * @return bool
     */
    public function isActive($store_id = null)
    {
        if ($store_id == null && $this->getStoreId() > 0) {
            $store_id = $this->getStoreId();
        }

        return $this->scopeConfig->isSetFlag(
            'abzer/smtp/active',
            ScopeInterface::SCOPE_STORE,
            $store_id
        );
    }

    /**
     * Get local client name
     *
     * @param ScopeInterface::SCOPE_STORE $store
     * @return string
     */
    public function getConfigName($store_id = null)
    {
        return 'localhost';
    }

    /**
     * Get system config password
     *
     * @param ScopeInterface::SCOPE_STORE $store
     * @return string
     */
    public function getConfigPassword($store_id = null)
    {
        return $this->getConfigValue('password', $store_id);
    }

    /**
     * Get system config username
     *
     * @param ScopeInterface::SCOPE_STORE $store
     * @return string
     */
    public function getConfigUsername($store_id = null)
    {
        return $this->getConfigValue('username', $store_id);
    }

    /**
     * Get system config auth
     *
     * @param ScopeInterface::SCOPE_STORE $store
     * @return string
     */
    public function getConfigAuth($store_id = null)
    {
        return $this->getConfigValue('auth', $store_id);
    }

    /**
     * Get system config ssl
     *
     * @param ScopeInterface::SCOPE_STORE $store
     * @return string
     */
    public function getConfigSsl($store_id = null)
    {
        return $this->getConfigValue('protocol', $store_id);
    }

    /**
     * Get system config host
     *
     * @param ScopeInterface::SCOPE_STORE $store
     * @return string
     */
    public function getConfigSmtpHost($store_id = null)
    {
        return $this->getConfigValue('host', $store_id);
    }

    /**
     * Get system config port
     *
     * @param ScopeInterface::SCOPE_STORE $store
     * @return string
     */
    public function getConfigSmtpPort($store_id = null)
    {
        return $this->getConfigValue('port', $store_id);
    }
   
    /**
     * Get system config
     *
     * @param String path
     * @param ScopeInterface::SCOPE_STORE $store
     * @return string
     */
    public function getConfigValue($path, $store_id = null)
    {
        return $this->getScopeConfigValue(
            "abzer/smtp/{$path}",
            $store_id
        );
    }

    /**
     * Get Scope Config Value
     *
     * @param String path
     * @param ScopeInterface::SCOPE_STORE $store
     * @return mixed
     */
    public function getScopeConfigValue($path, $store_id = null)
    {
        //use global store id
        if ($store_id === null && $this->getStoreId() > 0) {
            $store_id = $this->getStoreId();
        }
        
        //return value from core config
        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $store_id
        );
    }

    /**
     * Get StoreId
     *
     * @return int|null
     */
    public function getStoreId()
    {
        return $this->storeId;
    }

    /**
     * Get StoreId
     *
     * @param int|null $storeId
     */
    public function setStoreId($storeId = null)
    {
        $this->storeId = $storeId;
    }
    
    /**
     * Is log enabled?
     *
     * @param int|null $storeId
     */
    public function isLogEnabled($store_id = null)
    {
        return (bool)$this->getConfigValue('log', $store_id);
    }
    
    /**
     * Clear log interval
     *
     * @param int|null $storeId
     */
    public function getClearLog($store_id = null)
    {
        return (string) $this->getConfigValue('clearlog', $store_id);
    }
}
