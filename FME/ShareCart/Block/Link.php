<?php

/**
 * FME Extensions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the fmeextensions.com license that is
 * available through the world-wide-web at this URL:
 * https://www.fmeextensions.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  FME
 * @author     Atta <support@fmeextensions.com>
 * @package   FME_ShareCart
 * @copyright Copyright (c) 2019 FME (http://fmeextensions.com/)
 * @license   https://fmeextensions.com/LICENSE.txt
 */

namespace FME\ShareCart\Block;

class Link extends \Magento\Framework\View\Element\Html\Link
{
    
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
       // \FME\Mediaappearance\Helper\Data $helper,
        array $data = []
    ) {
        //$this->_helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * @return Url string
     */

    public function getMediaUrl()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $media_dir = $objectManager->get('Magento\Store\Model\StoreManagerInterface')
                ->getStore()
                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $media_dir;
    }
    public function getHref()
    {
       // $url =  $this->_helper->getMediaappearanceUrl();
        //return $url;
    }
}