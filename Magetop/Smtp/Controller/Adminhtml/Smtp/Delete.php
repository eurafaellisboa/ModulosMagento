<?php
/**
 * Magetop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magetop.com license that is
 * available through the world-wide-web at this URL:
 * https://www.magetop.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magetop
 * @package     Magetop_RewardPoints
 * @copyright   Copyright (c) Magetop (http://www.magetop.com/)
 * @license     https://www.magetop.com/LICENSE.txt
 */

namespace Magetop\Smtp\Controller\Adminhtml\Smtp;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magetop\Smtp\Model\LogFactory;

/**
 * Class Delete
 * @package Magetop\Smtp\Controller\Adminhtml\Smtp
 */
class Delete extends Action
{
    /**
     * @var LogFactory
     */
    protected $logFactory;

    /**
     * Delete constructor.
     * @param Action\Context $context
     * @param LogFactory $logFactory
     */
    public function __construct(
        LogFactory $logFactory,
        Action\Context $context
    ) {
        parent::__construct($context);

        $this->logFactory = $logFactory;
    }

    /**
     * @return $this|ResponseInterface|ResultInterface
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        try {
            $logId = $this->getRequest()->getParam('id');
            $this->logFactory->create()->load($logId)->delete();
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(
                __('We can\'t process your request right now. %1', $e->getMessage())
            );
            $this->_redirect('*/smtp/log');

            return;
        }

        $this->messageManager->addSuccessMessage(
            __('A total of 1 record have been deleted.')
        );

        return $resultRedirect->setPath('*/smtp/log');
    }
}
