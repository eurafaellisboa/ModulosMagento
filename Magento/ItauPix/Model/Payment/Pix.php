<?php
namespace Magento\ItauPix\Model\Payment;

use Magento\Framework\UrlInterface;
use Magento\Sales\Model\Order;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order\Payment;
use Magento\ItauPix\Model\ItauPix;

class Pix extends \Magento\Payment\Model\Method\AbstractMethod
{

    protected $_code = 'itaupix';


    public function authorize(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        $itaupix = new ItauPix();
        $sale = $itaupix->addPayPix($payment, $amount);

        $payment->setTransactionId($sale->chave . '-authorization')
        ->setTxnType(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_VOID)
        ->setIsTransactionClosed(false)
        ->setIsTransactionPending(true);
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
