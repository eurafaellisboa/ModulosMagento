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
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Registry;
use Mageplaza\ProductFeed\Controller\Adminhtml\AbstractManageFeeds;
use Mageplaza\ProductFeed\Helper\Data;
use Mageplaza\ProductFeed\Model\FeedFactory;

/**
 * Class Save
 * @package Mageplaza\ProductFeed\Controller\Adminhtml\ManageFeeds
 */
class Save extends AbstractManageFeeds
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var JsonHelper
     */
    protected $jsonHelper;

    /**
     * Save constructor.
     * @param FeedFactory $feedFactory
     * @param Registry $coreRegistry
     * @param Context $context
     * @param JsonHelper $jsonHelper
     * @param Data $helperData
     */
    public function __construct(
        FeedFactory $feedFactory,
        Registry $coreRegistry,
        Context $context,
        JsonHelper $jsonHelper,
        Data $helperData
    )
    {
        parent::__construct($feedFactory, $coreRegistry, $context);

        $this->jsonHelper = $jsonHelper;
        $this->helperData = $helperData;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     * @throws \Zend_Serializer_Exception
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        $data = $this->getRequest()->getPost('feed');
        if (isset($data['fields_map']) && $data['fields_map']) {
            $data['fields_map'] = $this->jsonHelper->jsonEncode($data['fields_map']);
        }
        if (isset($data['category_map']) && $data['category_map']) {
            $data['category_map'] = $this->helperData->serialize($data['category_map']);
        }
        if (isset($data['cron_run_time']) && $data['cron_run_time']) {
            $data['cron_run_time'] = implode(',', $data['cron_run_time']);
        }
        $conditionData = $this->getRequest()->getPost('rule');
        $feed = $this->initFeed();
        $feed->addData($data);
        $feed->loadPost($conditionData);

        try {
            $feed->save();
            $this->messageManager->addSuccess(__('The feed has been saved.'));
            $this->_getSession()->setData('mageplaza_productfeed_feed_data', false);

            if ($this->getRequest()->getParam('back')) {
                $resultRedirect->setPath('mpproductfeed/*/edit', ['feed_id' => $feed->getId(), '_current' => true]);
            } else {
                $resultRedirect->setPath('mpproductfeed/*/');
            }

            return $resultRedirect;
        } catch (LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\RuntimeException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something went wrong while saving the Feed.'));
        }

        $this->_getSession()->setData('mageplaza_productfeed_feed_data', $data);

        $resultRedirect->setPath('mpproductfeed/*/edit', ['feed_id' => $feed->getId(), '_current' => true]);

        return $resultRedirect;
    }
}
