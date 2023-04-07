<?php

namespace Magento\ItauPix\Api;


class Client
{
  /**
   * @var string
   */
  var $base_url_oauth = 'https://api.itau.com.br/sandbox/api/oauth/token/';

  /**
   * @var string
   */
  var $base_url = 'https://api.itau.com.br/sandbox/';

  /**
   * @var string
   */
  var $typeEnvironment = 'gw-dev-app-key';

  /**
   * @var string
   */
  private $clientId;

  /**
   * @var string
   */
  private $clientSecret;

  public function __construct($clientId, $clientSecret)
  {
    $this->clientId = $clientId;
    $this->clientSecret = $clientSecret;
  }

  public function makeEnvironment($typeEnvironment)
  {

    if ($typeEnvironment == 'gw-app-key') {
      $this->base_url = 'https://secure.api.itau/';
      $this->base_url_oauth = 'https://sts.itau.com.br/api/oauth/token';
    }

    $this->typeEnvironment = $typeEnvironment;
  }

  public function getToken()
  {
    $files = $this->getFiles();

    $crtFile = '';
    $keyFile = '';

    if (isset($files[0]) && isset($files[1])) {
      $crtFile = $files[0];
      $keyFile = $files[1];
    }

    $clientId = $this->clientId;
    $clientSecret = $this->clientSecret;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $this->base_url_oauth);
    curl_setopt($ch, CURLOPT_PORT, 443);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_SSLCERT, $crtFile);
    curl_setopt($ch, CURLOPT_SSLKEY, $keyFile);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials&client_id=$clientId&client_secret=$clientSecret");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      "Content-Type: application/x-www-form-urlencoded",
      "x-itau-correlationID: 2",
      "x-itau-flowID: 1"
    ));
    $response = curl_exec($ch);

    curl_close($ch);
    $response = json_decode($response);

    if (!isset($response->access_token)) {
      throw new \Magento\Framework\Exception\LocalizedException(__(json_encode($response)));
    }

    return $response->access_token;
  }

  public function getFiles()
  {
    $fileCrt = $this->getCoreConfig("payment/itaupix/file_crt");
    $fileKey = $this->getCoreConfig("payment/itaupix/file_key");
    $crtFile = $this->getFilePath($fileCrt);
    $keyFile = $this->getFilePath($fileKey);

    return [$crtFile, $keyFile];
  }

  public function getCoreConfig($valor)
  {
    $scopeConfig = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\App\Config\ScopeConfigInterface');
    return $scopeConfig->getValue($valor, \Magento\Store\Model\ScopeInterface::SCOPE_STORES);
  }

  public function getFilePath($fileName)
  {
    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    $fileSystem = $objectManager->create('\Magento\Framework\Filesystem');
    $mediaPath = $fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath();

    return $mediaPath . "/keys/" . $fileName;
  }

  public function makePix($data)
  {
    $token =  $this->getToken();

    $files = $this->getFiles();

    $crtFile = '';
    $keyFile = '';

    if (isset($files[0]) && isset($files[1])) {
      $crtFile = $files[0];
      $keyFile = $files[1];
    }

    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => $this->base_url . 'pix_recebimentos/v2/cob',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_SSLCERT => $crtFile,
      CURLOPT_SSLKEY => $keyFile,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => json_encode($data),
      CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer ' . $token,
        'x-correlationID: ' . $data['chave'],
        'Content-Type: application/json'
      ),
    ));

    $response = curl_exec($curl);
    $response = json_decode($response);

    curl_close($curl);
    return $response;
  }

  public function consultingStatus($txid)
  {
    $token =  $this->getToken();
    $curl = curl_init();

    $files = $this->getFiles();
    
    $crtFile = '';
    $keyFile = '';

    if (isset($files[0]) && isset($files[1])) {
      $crtFile = $files[0];
      $keyFile = $files[1];
    }

    curl_setopt_array($curl, array(
      CURLOPT_URL => $this->base_url . 'pix_recebimentos/v2/cobv/' . $txid,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_SSLCERT => $crtFile,
      CURLOPT_SSLKEY => $keyFile,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json'
      ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    return  $response;
  }

  public function getPixCode($url)
  {
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://' . $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    return $response;
  }
}
