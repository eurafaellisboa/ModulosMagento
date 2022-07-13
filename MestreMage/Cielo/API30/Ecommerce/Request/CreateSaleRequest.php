<?php

namespace MestreMage\Cielo\API30\Ecommerce\Request;

use MestreMage\Cielo\API30\Ecommerce\Sale;
use MestreMage\Cielo\API30\Environment;
use MestreMage\Cielo\API30\Merchant;

/**
 * Class CreateSaleRequest
 *
 * @package MestreMage\Cielo\API30\Ecommerce\Request
 */
class CreateSaleRequest extends AbstractRequest
{

    private $environment;

    /**
     * CreateSaleRequest constructor.
     *
     * @param Merchant    $merchant
     * @param Environment $environment
     */
    public function __construct(Merchant $merchant, Environment $environment)
    {
        parent::__construct($merchant);

        $this->environment = $environment;
    }

    /**
     * @param $sale
     *
     * @return null
     * @throws \MestreMage\Cielo\API30\Ecommerce\Request\CieloRequestException
     * @throws \RuntimeException
     */
    public function execute($sale)
    {
        $url = $this->environment->getApiUrl() . '1/sales/';

        return $this->sendRequest('POST', $url, $sale);
    }

    /**
     * @param $json
     *
     * @return Sale
     */
    protected function unserialize($json)
    {
        return Sale::fromJson($json);
    }
}
