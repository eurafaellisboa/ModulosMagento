<?php
namespace Digitaria\RDMarketing\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Digitaria\RDMarketing\Service\Connect;
use Digitaria\RDMarketing\Model\ResourceModel\EventLogFactory;
use Digitaria\RDMarketing\Model\EventLogFactory as EventLogModelFactory;

class SendQuestion implements ObserverInterface
{
    protected $request;
    protected $connect;
    protected $eventLogFactory;
    protected $eventLogModelFactory;

    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        Connect $connect,
        EventLogFactory $eventLogFactory,
        EventLogModelFactory $eventLogModelFactory
    ) {
        $this->request = $request;
        $this->connect = $connect;
        $this->eventLogFactory = $eventLogFactory;
        $this->eventLogModelFactory = $eventLogModelFactory;
    }

    public function execute(Observer $observer)
    {
        $eventType = "enviou-duvida-produto";
        $email = $this->request->getParam('author_email');
        $name = $this->request->getParam('author_name');
        //$phone = $this->request->getParam('telephone');
        $conversionType = "Conversão";
        $getToken = $this->connect->getToken();
        $rdstationToken = $this->connect->getTokenFromDatabase();
        $googleAnalyticsCookie = $_COOKIE['__utmz'] ?? null;

        if ($this->connect->isTokenValid($rdstationToken)) {
            try {
                $data_arrays = [
                    'email' => $email,
                    'conversion_identifier' => 'enviou-duvida-produto',
                    'name' => $name,
                    //'mobile_phone' => $phone,
                    'c_utmz' => $googleAnalyticsCookie,
                    'legal_bases' => [
                        [
                            'category' => 'communications',
                            'type' => 'consent',
                            'status' => 'granted'
                        ]
                    ]
                ];

                $payload = [
                    'event_type' => 'CONVERSION',
                    'event_family' => 'CDP',
                    'payload' => $data_arrays
                ];

                $data_json = json_encode($payload);

                $url = "https://api.rd.services/platform/events";

                $curl = curl_init();
                curl_setopt_array($curl, [
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => $data_json,
                    CURLOPT_HTTPHEADER => [
                        "Content-Type: application/json",
                        "accept: application/json",
                        "Authorization: Bearer $rdstationToken"
                    ],
                ]);

                $response = curl_exec($curl);
                $err = curl_error($curl);
                curl_close($curl);

                $status = "Enviado para o RD";

                // Criação do objeto EventLog
                $eventLog = $this->eventLogModelFactory->create();
                $eventLog->setEventType($eventType);
                $eventLog->setEventDate(date('Y-m-d H:i:s'));
                $eventLog->setEmail($email);
                $eventLog->setConversion($conversionType);
                $eventLog->setStatus($status);

                // Salvar o objeto no banco de dados
                $eventLog->save();
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                $status = "Erro ao obter o token";

                // Criação do objeto EventLog
                $eventLog = $this->eventLogModelFactory->create();
                $eventLog->setEventType($eventType);
                $eventLog->setEventDate(date('Y-m-d H:i:s'));
                $eventLog->setEmail($email);
                $eventLog->setConversion($conversionType);
                $eventLog->setStatus($status);

                // Salvar o objeto no banco de dados
                $eventLog->save();
            }
        } else {
            $status = "Token inválido";

            // Criação do objeto EventLog
            $eventLog = $this->eventLogModelFactory->create();
            $eventLog->setEventType($eventType);
            $eventLog->setEventDate(date('Y-m-d H:i:s'));
            $eventLog->setEmail($email);
            $eventLog->setConversion($conversionType);
            $eventLog->setStatus($status);

            // Salvar o objeto no banco de dados
            $eventLog->save();
        }
    }
}
