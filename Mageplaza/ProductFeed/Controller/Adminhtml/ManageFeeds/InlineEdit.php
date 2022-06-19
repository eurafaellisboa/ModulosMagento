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

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Mageplaza\ProductFeed\Model\FeedFactory;

/**
 * Class InlineEdit
 * @package Mageplaza\ProductFeed\Controller\Adminhtml\ManageFeeds
 */
class InlineEdit extends Action
{
    /**
     * JSON Factory
     *
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * Feed Factory
     *
     * @var FeedFactory
     */
    protected $feedFactory;

    /**
     * InlineEdit constructor.
     * @param Context $context
     * @param JsonFactory $jsonFactory
     * @param FeedFactory $feedFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        FeedFactory $feedFactory
    )
    {
        parent::__construct($context);

        $this->jsonFactory = $jsonFactory;
        $this->feedFactory = $feedFactory;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];
        $feedItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && !empty($feedItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error'    => true,
            ]);
        }

        $key = array_keys($feedItems);
        $feedId = !empty($key) ? (int)$key[0] : '';
        /** @var \Mageplaza\ProductFeed\Model\Feed $feed */
        $feed = $this->feedFactory->create()->load($feedId);
        try {
            $feedData = $feedItems[$feedId];
            $feed->addData($feedData);
            $feed->save();
        } catch (LocalizedException $e) {
            $messages[] = $this->getErrorWithFeedId($feed, $e->getMessage());
            $error = true;
        } catch (\RuntimeException $e) {
            $messages[] = $this->getErrorWithFeedId($feed, $e->getMessage());
            $error = true;
        } catch (\Exception $e) {
            $messages[] = $this->getErrorWithFeedId(
                $feed,
                __('Something went wrong while saving the Feed.')
            );
            $error = true;
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error'    => $error
        ]);
    }

    /**
     * Add Feed id to error message
     *
     * @param \Mageplaza\ProductFeed\Model\Feed $feed
     * @param string $errorText
     * @return string
     */
    public function getErrorWithFeedId(\Mageplaza\ProductFeed\Model\Feed $feed, $errorText)
    {
        return '[Feed ID: ' . $feed->getId() . '] ' . $errorText;
    }
}
