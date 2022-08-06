<?php

namespace Digitaria\CartRuleActions\Controller\Adminhtml\Action;

class MassDelete extends \Magento\SalesRule\Controller\Adminhtml\Promo\Quote
{
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $ids = $this->getRequest()->getPost('ids');
        if(!is_array($ids)) {
            $this->messageManager->addError(__('Por favor, selecione os item(s).'));
        } else {
            try {
                foreach ($ids as $id) {
                    $model = $this->_objectManager->create('\Magento\SalesRule\Model\Rule')
                        ->load($id)
                        ->delete();
                }
                $this->messageManager->addSuccess(__('%1 regra(s) foi deletada com sucesso.', count($ids)));

            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        return $resultRedirect->setPath('sales_rule/promo_quote/');
    }
}
