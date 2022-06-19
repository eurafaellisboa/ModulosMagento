<?php

namespace Abzertech\Smtp\Controller\Adminhtml\Email;

use Abzertech\Smtp\Model\CoreFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;

class MassDelete extends \Magento\Backend\App\Action
{

    /**
     * @var Filter
     */
    protected $filter;
    /**
     *
     * @var CoreFactory
     */
    protected $coreFactory;
    /**
     * Report constructor.
     *
     * @param Context $context
     * @param CoreFactory $coreFactory
     * @param Filter $filter
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        CoreFactory $coreFactory,
        Filter $filter
    ) {
        $this->coreFactory = $coreFactory;
        $this->filter = $filter;
        parent::__construct($context);
    }

    /**
     * Default execute function.
     *
     * @return ResultFactory
     */
    public function execute()
    {
        $params = $this->getRequest()->getParam('id');
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        try {
            $deleted = 0;
            foreach ($params as $id) {
                $item = $this->coreFactory->create()->load($id);
                $item->delete();
                $deleted++;
            }
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(
                __('We can\'t process your request right now. %1', $e->getMessage())
            );
            $this->_redirect('abzertechsmtp/email/log');

            return;
        }
        $this->messageManager->addSuccessMessage(
            __('A total of %1 record(s) have been deleted.', $deleted)
        );

        return $resultRedirect->setPath('abzertechsmtp/email/log');
    }
}
