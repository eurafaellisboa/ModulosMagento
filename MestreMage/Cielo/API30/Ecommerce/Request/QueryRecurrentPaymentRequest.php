<?php

namespace MestreMage\Cielo\API30\Ecommerce\Request;

use MestreMage\Cielo\API30\Ecommerce\RecurrentPayment;
use MestreMage\Cielo\API30\Environment;
use MestreMage\Cielo\API30\Merchant;

/**
 * Class QueryRecurrentPaymentRequest
 *
 * @package MestreMage\Cielo\API30\Ecommerce\Request
 */
class QueryRecurrentPaymentRequest extends AbstractRequest
{

    private $environment;

    /**
     * QueryRecurrentPaymentRequest constructor.
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
     * @param $recurrentPaymentId
     *
     * @return null
     * @throws \MestreMage\Cielo\API30\Ecommerce\Request\CieloRequestException
     * @throws \RuntimeException
     */
    public function execute($recurrentPaymentId)
    {
        $url = $this->environment->getApiQueryURL() . '1/RecurrentPayment/' . $recurrentPaymentId;

        return $this->sendRequest('GET', $url);
    }

    /**
     * @param $json
     *
     * @return RecurrentPayment
     */
    protected function unserialize($json)
    {
        return RecurrentPayment::fromJson($json);
    }
}
