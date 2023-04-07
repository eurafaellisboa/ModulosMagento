<?php
/**

 */
namespace Magento\ItauPix\Block\Adminhtml\System\Config;


class Extensions extends \Magento\Config\Block\System\Config\Form\Fieldset
{
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $orderId = $this->getRequest()->getParam('key');

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $link = $storeManager->getStore()->getHomePageUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB).'*/admin/system_config/edit/section/payment/key/'.$orderId;


        return '<p>'.__('version:').' 1.0.3</p><p>'.__('To configure access:').' <a href="'.$link.'">'.__('Store').' > '.__('Sale').' > '.__('Payment Methods').' > '.__('Itau PIX').'</a></p>';
    }
}
