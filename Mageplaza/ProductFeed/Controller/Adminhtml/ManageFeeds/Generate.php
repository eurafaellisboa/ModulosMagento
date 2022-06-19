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
use Magento\Framework\Registry;
use Mageplaza\ProductFeed\Controller\Adminhtml\AbstractManageFeeds;
use Mageplaza\ProductFeed\Helper\Data;
use Mageplaza\ProductFeed\Model\FeedFactory;

/**
 * Class Generate
 * @package Mageplaza\ProductFeed\Controller\Adminhtml\ManageFeeds
 */
class Generate extends AbstractManageFeeds
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * Generate constructor.
     * @param FeedFactory $feedFactory
     * @param Registry $coreRegistry
     * @param Context $context
     * @param Data $helperData
     */
    public function __construct(
        FeedFactory $feedFactory,
        Registry $coreRegistry,
        Context $context,
        Data $helperData
    )
    {
        parent::__construct($feedFactory, $coreRegistry, $context);

        $this->helperData = $helperData;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $feed = $this->initFeed();
        $this->helperData->generateAndDeliveryFeed($feed, 1);
        $resultRedirect->setPath('mpproductfeed/*/edit', ['feed_id' => $feed->getId(), '_current' => true]);

        return $resultRedirect;
    }
}
