<?php

namespace Digitaria\RDMarketing\Helper;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class CurrentCustomer extends \Magento\Framework\App\Helper\AbstractHelper
{
protected $userContext;
 
    public function __construct(
        Context $context,
        UserContextInterface $userContext
    )
    {
        $this->userContext = $userContext;
        parent::__construct($context);
    }

  public function getCustomerId()
  {
    $customerId = $this->userContext->getUserId();
        return $customerId;
  }
}