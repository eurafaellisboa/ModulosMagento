<?php

namespace Magento\ValidaCpf\Controller\Index;

use Magento\Framework\App\Action\Action;

use Magento\Framework\App\ResponseInterface;

use Magento\Framework\Controller\ResultFactory;

class Index extends Action

{

    public function execute()

    {

        return $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);

    }

}