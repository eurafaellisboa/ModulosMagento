<?php

namespace Digitaria\CartRuleActions\Controller\Adminhtml\Action;

class Duplicate extends \Magento\SalesRule\Controller\Adminhtml\Promo\Quote
{
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $model = $this->_objectManager->create(\Magento\SalesRule\Model\Rule::class);
                $model->load($id);
                $newModel = $this->_objectManager->create(\Magento\SalesRule\Model\Rule::class);
                $newModel
                    ->setData($model->getData())
                    ->setId(null)
					->setCouponCode(null);
                $newModel->save();
                $this->messageManager->addSuccessMessage(__('Regra duplicada com sucesso.'));
                $this->_redirect('sales_rule/promo_quote/');
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('We can\'t duplicate the rule right now. Please review the log and try again.')
                );
                $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
                $this->_redirect('sales_rule/promo_quote/edit', ['id' => $this->getRequest()->getParam('id')]);
                return;
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a rule to duplicate.'));
        $this->_redirect('sales_rule/*/');
		//$this->_redirect('sales_rule/promo_quote/edit', ['id' => $this->getRequest()->getParam('id')]);
    }
}
