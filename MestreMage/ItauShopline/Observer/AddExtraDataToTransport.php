<?php

namespace MestreMage\ItauShopline\Observer;

use Magento\Framework\Event\ObserverInterface;

class AddExtraDataToTransport implements ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $transport = $observer->getEvent()->getTransport();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $base_url = $storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);

        $order = $objectManager->create('Magento\Sales\Model\Order')->loadByIncrementId($transport->getOrder()->getData('increment_id'));
        $title_boleto = $transport['payment_html'];
        if($order->getPayment()->getAdditionalInformation('itau_shopline_data_genereted')){
            $transport['payment_html'] = '<p>'.$title_boleto.'</p><a href="'.$base_url.'statusItauShopline?create_boleto='.$order->getPayment()->getAdditionalInformation('itau_shopline_data_genereted').'"  target="_blank" style="text-decoration: none;background-color: #32aeef;color: #fff;padding: 7px 20px;margin: 10px 0;display: block;width: 100px;text-align: center;border-radius: 5px;" >Gerar Boleto</a>';;
        }
    }
}