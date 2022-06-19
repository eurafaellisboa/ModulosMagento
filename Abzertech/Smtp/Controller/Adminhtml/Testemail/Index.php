<?php

namespace Abzertech\Smtp\Controller\Adminhtml\Testemail;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;

class Index extends Action
{

    /**
     * Default execute function.
     *
     * @return resultPageFactory
     */
    public function execute()
    {
        return $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);
    }

    /**
     * Is the user allowed to view the grid.
     *
     * @return bool
     */
    protected function isAllowed()
    {
        return $this->_authorization->isAllowed('Abzertech_Smtp');
    }
}
