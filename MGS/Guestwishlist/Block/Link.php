<?php

namespace MGS\Guestwishlist\Block;

class Link extends \Magento\Wishlist\Block\Link
{
    /**
     * @var \Magento\Wishlist\Helper\Data
     */
    protected $_guestHelper;
    
    /**
     * @var \Magento\Wishlist\Helper\Data
     */
    protected $_wishlistHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Wishlist\Helper\Data $wishlistHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Wishlist\Helper\Data $wishlistHelper,
        \MGS\Guestwishlist\Helper\Data $guestHelper,
        array $data = []
    ) {
        $this->_guestHelper = $guestHelper;
        parent::__construct($context, $wishlistHelper, $data);
    }

    /**
     * @return string
     */
    public function getHref()
    {
        if (!$this->_guestHelper->isLoggedIn() 
                && $this->_guestHelper->isModuleEnable()) {
            return $this->getUrl('guestwishlist');
        }
        return $this->getUrl('wishlist');
    }
}
