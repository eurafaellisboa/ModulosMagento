<?php
namespace Digitaria\RDMarketing\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Digitaria\RDMarketing\Service\Connect;
use Digitaria\RDMarketing\Model\ResourceModel\EventLogFactory;
use Digitaria\RDMarketing\Model\EventLogFactory as EventLogModelFactory;

class RegisterSuccess implements ObserverInterface
{
    protected $_request;
    protected $_customerSession;
    protected $connect;
    protected $eventLogFactory;
    protected $eventLogModelFactory;

    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Customer\Model\Session $customerSession,
        Connect $connect,
        EventLogFactory $eventLogFactory,
        EventLogModelFactory $eventLogModelFactory
    ) {
        $this->_request = $request;
        $this->_customerSession = $customerSession;
        $this->connect = $connect;
        $this->eventLogFactory = $eventLogFactory;
        $this->eventLogModelFactory = $eventLogModelFactory;
    }

    public function execute(Observer $observer)
    {
		 
        
        $googleAnalyticsCookie = $this->readCookie("__utmz");
        $sourceCookie = $this->readCookie("__trf.src");

		 $customer = $observer->getEvent()->getCustomer();
         $name = $customer->getFirstName() . ' ' . $customer->getLastName();
         $email = $customer->getEmail();
         $telephone = $customer->getTelephone();
         $city = $customer->getCity();
         $dob = $customer->getDob();
         
         $conversionType = "Conversão";
		 $eventType = 'novo-cadastro-cliente';
		 $getToken = $this->connect->getToken();
         $rdstationToken = $this->connect->getTokenFromDatabase();
         //$googleAnalyticsCookie = $_COOKIE['__utmz'] ?? null;
         
            if ($this->connect->isTokenValid($rdstationToken)) {
        try {
            
                
                $curl = curl_init();
                $data_arrays = [
                    'email' => $email,
                    'conversion_identifier' => $eventType,
                    'name' => $name,
                    'birthdate' => $dob,
                    'mobile_phone' => $telephone,
                    'city' => $city,
                    'c_utmz' => $googleAnalyticsCookie,
                    'traffic_source' => $sourceCookie,
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
                $eventLog = $this->eventLogModelFactory->create();
                $eventLog->setEventType($eventType);
                $eventLog->setEventDate(date('Y-m-d H:i:s'));
                $eventLog->setEmail($email);
				$eventLog->setConversion($conversionType);
                $eventLog->setStatus($status);
                $eventLog->save();
                return $this;
            
        } catch (\Exception $e) {
			  
			    $status = "Erro ao obter o token";
                $eventLog = $this->eventLogModelFactory->create();
                $eventLog->setEventType($eventType);
                $eventLog->setEventDate(date('Y-m-d H:i:s'));
                $eventLog->setEmail($email);
				$eventLog->setConversion($conversionType);
                $eventLog->setStatus($status);
                $eventLog->save();
        }
					
        } else {
					
				$status = "Token inválido";	
                $eventLog = $this->eventLogModelFactory->create();
                $eventLog->setEventType($eventType);
                $eventLog->setEventDate(date('Y-m-d H:i:s'));
                $eventLog->setEmail($email);
				$eventLog->setConversion($conversionType);
                $eventLog->setStatus($status);
                $eventLog->save();
           }
    }

    private function readCookie($name)
    {
        if (isset($_COOKIE[$name])) {
            return $_COOKIE[$name];
        }
        return null;
    }

}
