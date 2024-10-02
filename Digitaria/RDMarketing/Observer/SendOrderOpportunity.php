<?php

namespace Digitaria\RDMarketing\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\App\RequestInterface;
use Digitaria\RDMarketing\Service\Connect;
use Digitaria\RDMarketing\Model\OrderData;
use Magento\Checkout\Model\Session;
use Digitaria\RDMarketing\Model\ResourceModel\EventLogFactory;
use Digitaria\RDMarketing\Model\EventLogFactory as EventLogModelFactory;

class SendOrderOpportunity implements ObserverInterface
{
    protected $connect;
    protected $orderData;
	 protected $checkoutSession;
    protected $eventLogFactory;
    protected $eventLogModelFactory;
	
    public function __construct(
        Connect $connect,
        OrderData $orderData,
		  Session $checkoutSession,
        EventLogFactory $eventLogFactory,
        EventLogModelFactory $eventLogModelFactory
    ) {
        $this->connect = $connect;
        $this->orderData = $orderData;
		  $this->checkoutSession = $checkoutSession;
        $this->eventLogFactory = $eventLogFactory;
        $this->eventLogModelFactory = $eventLogModelFactory;
    }


    public function execute(Observer $observer)
    {
		 
		    $conversionType = "Marcação de Oportunidade";
		    $eventType = "novo-pedido";
		  	//$orderId = $this->request->getParam('order_id');
             //$order = $this->checkoutSession->getLastRealOrderId();
		    $order = $this->checkoutSession->getLastRealOrder();
		    $orderId = $order->getId();
            // Obter os dados do pedido
            $orderData = $this->orderData->getOrderData($orderId);
            $name = $orderData['customer_name'];
            $email = $orderData['customer_email'];
			$paymentMethod = $orderData['payment_method'];

           // Define o valor do campo cf_order_payment_method com base no método de pagamento
           if (stripos($paymentMethod, 'crédito') !== false) {
            $cfOrderPaymentMethod = 'Credit Card';
           } elseif (stripos($paymentMethod, 'débito') !== false) {
            $cfOrderPaymentMethod = 'Debit Card';
           } else {
            $cfOrderPaymentMethod = 'Others';
           }
		 
		    $getToken = $this->connect->getToken();
            $rdstationToken = $this->connect->getTokenFromDatabase();

            if ($this->connect->isTokenValid($rdstationToken)) {

        try {

      
					
                // Evento OPPORTUNITY
					 $conversionType = "Marcação de Oportunidade";
                $opportunityEvent = [
                    'event_type' => 'OPPORTUNITY',
                    'event_family' => 'CDP',
                    'payload' => [
                        'name' => $name,
                        'email' => $email,
                        'funnel_name' => 'default',
                        'legal_bases' => [
                            [
                                'category' => 'communications',
                                'type' => 'consent',
                                'status' => 'granted'
                            ]
                        ]
                    ]
                ];

                // Evento ORDER_PLACED
					 $conversionType = "Compra Iniciada";
                $orderPlacedEvent = [
                    'event_type' => 'ORDER_PLACED',
                    'event_family' => 'CDP',
                    'payload' => [
                        'name' => $name,
                        'email' => $email,
                        'cf_order_id' => $orderData['order_id'],
                        'cf_order_total_items' => $orderData['order_items_count'],
                        'cf_order_status' => $orderData['order_status'],
                        'cf_order_payment_method' => $this->getPaymentMethod($orderData['payment_method']),
                        'cf_order_payment_amount' => floatval($orderData['order_total']),
                        'cf_shipping_method' => $orderData['shipping_method'],
                        'cf_order_date' => $orderData['order_date'],
                        'cf_coupon_code' => $orderData['coupon_code'],
                    ]
                ];
					
					
					// Conversão NOVO PEDIDO
					 $conversionType = "Conversão";
                $newOrderEvent = [
                    'event_type' => 'CONVERSION',
                    'event_family' => 'CDP',
                    'payload' => [
							   'conversion_identifier' => 'novo-pedido',
                               'email' => $email,
							   'city' => $orderData['customer_city'],
							   //'state' => $cfOrderPaymentMethod,
							   'mobile_phone' => $orderData['customer_phone'],
							   'birthdate' => $orderData['customer_birthday'],
		
                    ]
                ];

                $data_arrays = [$opportunityEvent, $orderPlacedEvent, $newOrderEvent];
                
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
					
					 $events = [$opportunityEvent, $orderPlacedEvent, $newOrderEvent];

                foreach ($events as $event) {
						 
				$conversionType = $this->getConversionType($event['event_type']);
                $eventType = $this->getEventType($event['event_type']);
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
                $eventLog->save();
            }
    }
	
	
	protected function getPaymentMethod($paymentMethod)
    {
        $paymentMethod = strtolower($paymentMethod);

        if (strpos($paymentMethod, 'crédito') !== false) {
            return 'Credit Card';
        } elseif (strpos($paymentMethod, 'débito') !== false) {
            return 'Debit Card';
        } else {
            return 'Others';
        }
	}

    protected function getEventType($eventType)
    {
        if ($eventType === 'OPPORTUNITY') {
            return 'marcacao-de-oportunidade';
        } elseif ($eventType === 'ORDER_PLACED') {
            return 'compra-iniciada';
        } elseif ($eventType === 'CONVERSION') {
            return 'novo-pedido';
        }

            return $eventType;
    }

    protected function getConversionType($eventType)
    {
        if ($eventType === 'OPPORTUNITY') {
            return 'Marcação de Oportunidade';
        } elseif ($eventType === 'ORDER_PLACED') {
            return 'Compra Iniciada';
        } elseif ($eventType === 'CONVERSION') {
            return 'Conversão';
        }
            return $eventType;
    }

}
