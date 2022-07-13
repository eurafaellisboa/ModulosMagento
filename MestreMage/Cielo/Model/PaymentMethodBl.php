<?php
namespace MestreMage\Cielo\Model;

use \Magento\Framework\UrlInterface;
use \Magento\Payment\Model\Method\AbstractMethod;
use \Magento\Sales\Model\Order;
use \Magento\Framework\Exception\LocalizedException;
use \Magento\Sales\Model\Order\Payment;


class PaymentMethodBl extends \Magento\Payment\Model\Method\AbstractMethod
{

    protected $_code = 'mestremagebl';


    public function authorize(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $call_boleto = $objectManager->get('MestreMage\Cielo\Helper\Data');
        $call_boleto->addPayBoleto($payment, $amount);
        return $this;


    }
    public function assignData(\Magento\Framework\DataObject $data)
    {
        parent::assignData($data);
        $infoInstance = $this->getInfoInstance();
        $currentData = $data->getAdditionalData();
        foreach($currentData as $key=>$value){
			if($key == 'extension_attributes'){
                $value = ['mm'];
            }
            $infoInstance->setAdditionalInformation($key,$value);
        }
        return $this;
    }


}
