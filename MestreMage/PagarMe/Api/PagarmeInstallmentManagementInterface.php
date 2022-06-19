<?php declare(strict_types=1);


namespace MestreMage\PagarMe\Api;


interface PagarmeInstallmentManagementInterface
{

    /**
     * GET for PagarmeInstallment api
     * @param string $param
     * @return string
     */
    public function getPagarmeInstallment($param);
}