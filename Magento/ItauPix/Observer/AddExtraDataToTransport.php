<?php

namespace Magento\ItauPix\Observer;

use Magento\Framework\Event\ObserverInterface;

class AddExtraDataToTransport implements ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $transport = $observer->getEvent()->getTransport();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $order = $objectManager->create('Magento\Sales\Model\Order')->loadByIncrementId($transport->getOrder()->getData('increment_id'));
        $title_boleto = $transport['payment_html'];
        if($order->getPayment()->getAdditionalInformation('itaupix_boleto_url')){
                $transport['payment_html'] = '<p>'.$title_boleto.'</p><a href="'.$order->getPayment()->getAdditionalInformation('itaupix_boleto_url').'"  target="_blank" style="text-decoration: none;background-color: #32aeef;color: #fff;padding: 7px 20px;margin: 10px 0;display: block;width: 100px;text-align: center;border-radius: 5px;" >Gerar Boleto</a>';
        }

        if($order->getPayment()->getAdditionalInformation('itaupix_pix_qr_code')){
            $transport['payment_html'] = '<p>'.$title_boleto.'</p><p style="font-size: 12px; width:200px; word-wrap: break-word; overflow-wrap: break-word;"><b>Pix Copia e Cola:</b><br />'.$order->getPayment()->getAdditionalInformation('itaupix_pix_qr_code').'</p>';
        }
    }
}