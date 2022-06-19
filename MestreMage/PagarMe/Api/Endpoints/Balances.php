<?php

namespace MestreMage\PagarMe\Api\Endpoints;

use MestreMage\PagarMe\Api\Client;
use MestreMage\PagarMe\Api\Routes;
use MestreMage\PagarMe\Api\Endpoints\Endpoint;

class Balances extends Endpoint
{
    /**
     * @return \ArrayObject
     */
    public function get()
    {
        return $this->client->request(
            self::GET,
            Routes::balances()->base()
        );
    }
}
