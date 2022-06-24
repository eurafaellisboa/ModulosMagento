<?php
/**
 * Class CheckoutData
 *
 * PHP version 7
 *
 * @category Sparsh
 * @package  Sparsh_AjaxLayeredNavigation
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
namespace Sparsh\AjaxLayeredNavigation\Plugin;

/**
 * Class CheckoutData
 *
 * @category Sparsh
 * @package  Sparsh_AjaxLayeredNavigation
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
class CheckoutData
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Recipient email config path
     */
    const XML_PATH_AJAXLAYERED_ENABLED = 'sparsh_ajax_layered_navigation/ajax_layered_navigation/enabled';

    /**
     * CheckoutData constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param \Magento\Checkout\Block\Cart\Sidebar $subject
     * @param $result
     * @return mixed
     */
    public function afterGetConfig(\Magento\Checkout\Block\Cart\Sidebar $subject, $result)
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $isEnabled = $this->scopeConfig->getValue(self::XML_PATH_AJAXLAYERED_ENABLED, $storeScope);
        if ($isEnabled) {
            $result['isSparshALNEnable'] = 1;
        } else {
            $result['isSparshALNEnable'] = 0;
        }

        return $result;
    }
}
