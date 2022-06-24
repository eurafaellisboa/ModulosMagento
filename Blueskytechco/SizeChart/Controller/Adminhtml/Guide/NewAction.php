<?php
namespace Blueskytechco\SizeChart\Controller\Adminhtml\Guide;

use Magento\Framework\Controller\ResultFactory;
use Blueskytechco\SizeChart\Controller\Adminhtml\AbstractStore;

/**
 * Class Save
 *
 * @package Blueskytechco\SizeChart\Controller\Adminhtml\Guide
 */
class NewAction extends AbstractStore
{
    /**
     * @return \Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Forward $resultForward */
        $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
        return $resultForward->forward('edit');
    }
}
