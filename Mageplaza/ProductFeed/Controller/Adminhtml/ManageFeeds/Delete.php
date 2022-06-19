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

use Mageplaza\ProductFeed\Controller\Adminhtml\AbstractManageFeeds;

/**
 * Class Delete
 * @package Mageplaza\ProductFeed\Controller\Adminhtml\AbstractManageFeeds
 */
class Delete extends AbstractManageFeeds
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        /** @var \Mageplaza\ProductFeed\Model\Feed $feed */
        $feed = $this->initFeed();
        if ($feed->getId()) {
            try {
                $feed->delete();
                $this->messageManager->addSuccess(__('The Feed has been deleted.'));
                $resultRedirect->setPath('mpproductfeed/*/');

                return $resultRedirect;
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());

                // go back to edit form
                $resultRedirect->setPath('mpproductfeed/*/edit', ['feed_id' => $feed->getId()]);

                return $resultRedirect;
            }
        }
        // display error message
        $this->messageManager->addError(__('The Feed to delete was not found.'));

        $resultRedirect->setPath('mpproductfeed/*/');

        return $resultRedirect;
    }
}
