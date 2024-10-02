<?php

namespace Digitaria\RDMarketing\Service;

use GuzzleHttp\Client;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use GuzzleHttp\Exception\ClientException;
use \Zend\Log\Logger;
use \Zend\Log\Writer\Stream;

class Connect
{
    private $clientId;
    private $clientSecret;
    private $code;
    private $accessToken;
    private $refreshToken;
    private $tokenExpires;
    private $client;
    private $scopeConfig;
    private $resourceConnection;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Client $client,
        ResourceConnection $resourceConnection
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->clientId = $scopeConfig->getValue('rdmarketing_api/authentication/client_id');
        $this->clientSecret = $scopeConfig->getValue('rdmarketing_api/authentication/client_secret');
        $this->code = $scopeConfig->getValue('rdmarketing_api/authentication/code');
        $this->client = $client;
        $this->resourceConnection = $resourceConnection;
    }

    public function getTokenFromDatabase()
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('rdstationmarketing_token');
        $select = $connection->select()
            ->from($tableName, ['token', 'expire_time'])
            ->order('token_id DESC')
            ->limit(1);

        $tokenData = $connection->fetchRow($select);

        if ($tokenData && $tokenData['expire_time'] > time()) {
            return $tokenData['token'];
        }

        return null;
    }

    public function getRefreshTokenFromDatabase()
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('rdstationmarketing_token');
        $select = $connection->select()
            ->from($tableName, ['refresh_token', 'expire_time'])
            ->order('token_id DESC')
            ->limit(1);

        $tokenData = $connection->fetchRow($select);

        if ($tokenData) {
            return $tokenData['refresh_token'];
        }

        return null;
    }

    public function isTokenValid($rdstationToken)
    {
        return $rdstationToken !== null;
    }

    public function getToken()
    {
        $writer = new Stream(BP . '/var/log/token.log');
        $logger = new Logger();
        $logger->addWriter($writer);
        $logger->info('Get Token');

        try {
            $rdstationToken = $this->getTokenFromDatabase();

            if ($rdstationToken !== null) {
                return $rdstationToken;
            }

            $refreshToken = $this->getRefreshTokenFromDatabase();

            if ($refreshToken !== null) {
                $response = $this->client->request('POST', 'https://api.rd.services/auth/token', [
                    'body' => json_encode([
                        'client_id' => $this->clientId,
                        'client_secret' => $this->clientSecret,
                        'refresh_token' => $refreshToken,
                    ]),
                    'headers' => [
                        'accept' => 'application/json',
                        'content-type' => 'application/json',
                    ],
                ]);

                $responseData = json_decode($response->getBody(), true);
                $this->accessToken = $responseData['access_token'];
                $this->refreshToken = $responseData['refresh_token'];
                $this->tokenExpires = time() + 86400;
                $token = $this->accessToken;

                $this->saveToken($this->accessToken, $this->refreshToken, $this->tokenExpires, $logger);
                $logger->info('Token refreshed');
                return $token;
            } else {
                $response = $this->client->request('POST', 'https://api.rd.services/auth/token', [
                    'body' => json_encode([
                        'client_id' => $this->clientId,
                        'client_secret' => $this->clientSecret,
                        'code' => $this->code,
                    ]),
                    'headers' => [
                        'accept' => 'application/json',
                        'content-type' => 'application/json',
                    ],
                ]);

                $responseData = json_decode($response->getBody(), true);
                $this->accessToken = $responseData['access_token'];
                $this->refreshToken = $responseData['refresh_token'];
                $this->tokenExpires = time() + 86400;
                $token = $this->accessToken;

                $this->saveToken($this->accessToken, $this->refreshToken, $this->tokenExpires, $logger);
                $logger->info('Token generated');
                return $token;
            }
        } catch (ClientException $e) {
            // Tratamento do erro de autenticação
            $logger->err('Authentication error: ' . $e->getMessage());
            $logger->info('melour');
            return null;
        }
    }

    private function saveToken($token, $refreshToken, $expireTime, $logger)
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('rdstationmarketing_token');

        $select = $connection->select()
            ->from($tableName, ['token_id'])
            ->order('token_id DESC')
            ->limit(1);

        $tokenData = $connection->fetchRow($select);

        if ($tokenData) {
            $tokenId = $tokenData['token_id'];
            $connection->update($tableName, [
                'token' => $token,
                'refresh_token' => $refreshToken,
                'expire_time' => $expireTime,
                'generate_time' => time()
            ], ['token_id = ?' => $tokenId]);
            $logger->info('nao tem tokendata');
        } else {
            $connection->insert($tableName, [
                'token' => $token,
                'refresh_token' => $refreshToken,
                'expire_time' => $expireTime,
                'generate_time' => time()
            ]);
            $logger->info('tem token data');
        }
    }

    // Outros métodos da classe
}
