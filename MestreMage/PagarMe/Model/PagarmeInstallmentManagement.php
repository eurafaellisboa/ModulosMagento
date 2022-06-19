<?php declare(strict_types=1);


namespace MestreMage\PagarMe\Model;

use MestreMage\PagarMe\Api\Client;

class PagarmeInstallmentManagement implements \MestreMage\PagarMe\Api\PagarmeInstallmentManagementInterface
{

    /**
     * {@inheritdoc}
     */
    public function getPagarmeInstallment($param)
    {
        $max_installments = (int)$this->getCoreConfig('payment/pagarmecc/max_installments');
        $valorminimo = $this->getCoreConfig('payment/pagarmecc/minimo_value_parcel');
        $parcel = 1;
        for ($x = 1;$x <= $max_installments;$x++){
            $final = ($param / $x);
            if($final >= (int)$valorminimo) {
                $parcel =  $x;
            }
        }
        $client = new Client($this->getCoreConfig('payment/pagarmeconfig/api_key'));
        $calculateInstallments = $client->transactions()->calculateInstallments([
            'amount' =>str_replace(".", "", number_format((float)$param, 2, '.', '')),
            'free_installments' => $this->getCoreConfig('payment/pagarmecc/free_installments'),
            'max_installments' => $parcel,
            'interest_rate' => floatval(str_replace(',','.',$this->getCoreConfig('payment/pagarmecc/interest_rate')))
        ]);

        return json_encode($calculateInstallments);
    }
    public function getCoreConfig($valor){
        $scopeConfig = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\App\Config\ScopeConfigInterface');
        return $scopeConfig->getValue($valor, \Magento\Store\Model\ScopeInterface::SCOPE_STORES);
    }
}
