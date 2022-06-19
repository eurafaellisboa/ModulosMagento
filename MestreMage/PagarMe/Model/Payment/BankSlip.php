<?php
namespace MestreMage\PagarMe\Model\Payment;

use Magento\Framework\UrlInterface;
use Magento\Sales\Model\Order;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order\Payment;
use MestreMage\PagarMe\Model\PagarMe;

class BankSlip extends \Magento\Payment\Model\Method\AbstractMethod
{

    protected $_code = 'pagarmebl';


    public function authorize(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        $pagar_me = new PagarMe();
        $pagar_me->addPayBoleto($payment, $amount);

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
