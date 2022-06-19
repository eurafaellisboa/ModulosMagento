<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_ProductFeed
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ProductFeed\Controller\Adminhtml\ManageFeeds;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\ProductFeed\Controller\Adminhtml\AbstractManageFeeds;
use Mageplaza\ProductFeed\Model\FeedFactory;

/**
 * Class Products
 * @package Mageplaza\ProductFeed\Controller\Adminhtml\ManageFeeds
 */
class Products extends AbstractManageFeeds
{
    /**
     * JS helper
     *
     * @var \Magento\Backend\Helper\Js
     */
    protected $jsonHelper;

    /**
     * @var PageFactory
     */
    protected $pageFactory;

    /**
     * Products constructor.
     * @param FeedFactory $feedFactory
     * @param Registry $coreRegistry
     * @param Context $context
     * @param PageFactory $pageFactory
     * @param JsonHelper $jsonHelper
     */
    public function __construct(
        FeedFactory $feedFactory,
        Registry $coreRegistry,
        Context $context,
        PageFactory $pageFactory,
        JsonHelper $jsonHelper
    )
    {
        parent::__construct($feedFactory, $coreRegistry, $context);

        $this->jsonHelper = $jsonHelper;
        $this->pageFactory = $pageFactory;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $page = $this->pageFactory->create();
        $html = $page->getLayout()
            ->createBlock('\Mageplaza\ProductFeed\Block\Adminhtml\Feed\Edit\Tab\Renderer\Products')->toHtml();
        if ($this->getRequest()->getParam('loadGrid')) {
            $html = $this->jsonHelper->jsonEncode($html);
        }

        return $this->getResponse()->representJson($html);
    }
}
