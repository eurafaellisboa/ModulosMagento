<?php
namespace Digitaria\CartRuleActions\Block\Adminhtml;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DuplicateButton extends \Magento\SalesRule\Block\Adminhtml\Promo\Quote\Edit\GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     * @codeCoverageIgnore
     */
    public function getButtonData()
    {
        $data = [];
        $ruleId = $this->getRuleId();
        if ($this->canRender('save')) {
            $data = [
                'label' => __('Duplicate'),
                'class' => 'save',
                'on_click' => sprintf("location.href = '%s';", $this->urlBuilder->getUrl('digitaria_cartruleactions/action/duplicate', ['id' => $ruleId])),
                'sort_order' => 1
            ];
        }
        return $data;
    }
}
