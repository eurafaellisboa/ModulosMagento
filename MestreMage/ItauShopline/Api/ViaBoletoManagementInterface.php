<?php
declare(strict_types=1);

namespace MestreMage\ItauShopline\Api;

interface ViaBoletoManagementInterface
{

    /**
     * GET for viaBoleto api
     * @param string $param
     * @return string
     */
    public function getViaBoleto($param);
}

