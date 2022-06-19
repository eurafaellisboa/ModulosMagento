<?php

namespace MestreMage\PagarMe\Api\Endpoints;

use MestreMage\PagarMe\Api\Client;
use MestreMage\PagarMe\Api\Routes;
use MestreMage\PagarMe\Api\Endpoints\Endpoint;

class Refunds extends Endpoint
{
    /**
     * @param array|null $payload
     *
     * @return \ArrayObject
     */
    public function getList(array $payload = null)
    {
        return $this->client->request(
            self::GET,
            Routes::refunds()->base(),
            ['query' => $payload]
        );
    }
}
