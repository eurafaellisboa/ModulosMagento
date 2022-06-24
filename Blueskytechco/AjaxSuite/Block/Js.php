<?php
namespace Blueskytechco\AjaxSuite\Block;

use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\View\Element\Template\Context;

class Js extends \Magento\Theme\Block\Html\Header\Logo
{
    /**
     * @var string
     */
    protected $_template = 'default.phtml';

    protected $customerSession;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageHelper,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    ) {
        parent::__construct($context, $fileStorageHelper, $data);

        $this->customerSession = $customerSession;
    }

    /**
     * @return mixed
     */
    public function getCustomerId()
    {
        return $this->customerSession->getCustomer()->getId();
    }

    /**
     * @param string $group
     * @return mixed
     */
    public function getSettingStatus($group = 'general')
    {
		return $this->_scopeConfig->getValue('ajaxsuite/' . $group . '/enabled', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param $path
     * @return mixed
     */
    public function getConfigValue($path)
    {
            return $this->_scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param $path
     * @return mixed
     */
    public function getModuleConfigValue($path)
    {
         return $this->_scopeConfig->getValue('ajaxsuite/' . $path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
