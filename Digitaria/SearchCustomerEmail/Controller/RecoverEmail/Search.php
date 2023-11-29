<?php
namespace Digitaria\SearchCustomerEmail\Controller\RecoverEmail;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Customer\Model\CustomerFactory;

class Search extends \Magento\Framework\App\Action\Action
{
    protected $jsonFactory;
    protected $customerFactory;
    
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        CustomerFactory $customerFactory
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->customerFactory = $customerFactory;
        parent::__construct($context);
    }
    
 public function execute()
{
    $result = $this->jsonFactory->create();
    
    $taxvat = $this->getRequest()->getParam('taxvat');
    
    $customerCollection = $this->customerFactory->create()->getCollection();
    $customerCollection->addAttributeToSelect('*');
    $customerCollection->addAttributeToFilter('taxvat', ['eq' => $taxvat]);
    
    if ($customerCollection->getSize() > 0) {
        $customer = $customerCollection->getFirstItem();
        $result->setData(['success' => true, 'email' => $customer->getEmail()]);
    } else {
        $result->setData(['success' => false]);
    }
    
    return $result;
}

}
