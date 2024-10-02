<?php
namespace Digitaria\RDMarketing\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\App\RequestInterface;
use Magento\Sales\Model\Order;
use Digitaria\RDMarketing\Service\Connect;
use Digitaria\RDMarketing\Model\OrderData;
use Digitaria\RDMarketing\Model\ResourceModel\EventLogFactory;
use Digitaria\RDMarketing\Model\EventLogFactory as EventLogModelFactory;

class OrderSaveAfter implements ObserverInterface
{
    protected $connect;
    protected $orderData;
    protected $eventLogFactory;
    protected $eventLogModelFactory;

    public function __construct(
        Connect $connect,
        OrderData $orderData,
        EventLogFactory $eventLogFactory,
        EventLogModelFactory $eventLogModelFactory
    ) {
        $this->connect = $connect;
        $this->orderData = $orderData;
        $this->eventLogFactory = $eventLogFactory;
        $this->eventLogModelFactory = $eventLogModelFactory;
    }

    public function execute(Observer $observer)
    {
		 
		 $order = $observer->getOrder();
         $orderId = $order->getId();
         $orderData = $this->orderData->getOrderData($orderId);
		 $statusLabel = $order->getStatusLabel();
		 $eventType = 'status-pedido';
		 $eventType = $eventType . ' (' . $statusLabel . ')';
		 $conversionType = "Conversão";
         $email = $orderData['customer_email'];
		 
         $getToken = $this->connect->getToken();
         $rdstationToken = $this->connect->getTokenFromDatabase();
         
        if ($this->connect->isTokenValid($rdstationToken)) {
		 
        try {
			  
                if ($order->getStatus() == 'canceled' || $order->getStatus() == 'closed') {
                    // Evento OPPORTUNITY LOST
					$conversionType = "Oportunidade Perdida";
                    $opportunityLostEvent = [
                        'event_type' => 'OPPORTUNITY_LOST',
                        'event_family' => 'CDP',
                        'payload' => [
                            'email' => $email,
                            'funnel_name' => 'default',
                            'reason' => 'Pedido cancelado'
                        ]
                    ];

                $data_arrays = [$opportunityLostEvent];
                } elseif ($order->getStatus() == 'processing') {
                    // Evento OPPORTUNITY WON
					$conversionType = "Marcação de Venda";
                    $opportunityWonEvent = [
                        'event_type' => 'SALE',
                        'event_family' => 'CDP',
                        'payload' => [
                            'email' => $email,
                            'funnel_name' => 'default',
                            'value' => floatval($orderData['order_total']),
                        ]
                    ];

                $data_arrays = [$opportunityWonEvent];
                } elseif ($order->getStatus() == 'complete') {
                    // Evento OPPORTUNITY WON
					$conversionType = "Pedido Enviado";
                    $paymentApproved = [
                    'event_type' => 'CONVERSION',
                    'event_family' => 'CDP',
                    'payload' => [
                        'conversion_identifier' => 'pedido-enviado',
                        'email' => $email,

                    ]
                    ];

                    $data_arrays = [$paymentApproved];
                }


                // Conversão UPDATE PEDIDO
				$conversionType = "Conversão";
                $updateOrderEvent = [
                    'event_type' => 'CONVERSION',
                    'event_family' => 'CDP',
                    'payload' => [
                        'conversion_identifier' => 'status-pedido',
                        'email' => $email,
                        'cf_order_status' => $orderData['order_status'],

                    ]
                ];

                $data_arrays[] = $updateOrderEvent;

                $data_json = json_encode($data_arrays);

                $url = "https://api.rd.services/platform/events/batch";

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
						
                foreach ($data_arrays as $event) {
                // Define os valores de $eventType e $conversionType de acordo com o evento
                $eventType = $this->getEventType($event['event_type'], $statusLabel);
                $conversionType = $this->getConversionType($event['event_type']);
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
            }
            
        } catch (\Exception $e) {
			  
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
	
	protected function getEventType($eventType, $statusLabel)
{
    if ($eventType === 'OPPORTUNITY_LOST') {
        return 'oportunidade-perdida (' . $statusLabel . ')';
    } elseif ($eventType === 'SALE') {
        return 'marcacao-de-venda (' . $statusLabel . ')';
    } elseif ($eventType === 'CONVERSION') {
        return 'status-pedido (' . $statusLabel . ')';
    }

    return $eventType;
}

protected function getConversionType($eventType)
{
    if ($eventType === 'OPPORTUNITY_LOST') {
        return 'Oportunidade Perdida';
    } elseif ($eventType === 'SALE') {
        return 'Marcação de Venda';
    } elseif ($eventType === 'CONVERSION') {
        return 'Conversão';
    }

    return $eventType;
}
}


