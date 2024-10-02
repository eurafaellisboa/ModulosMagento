<?php 
namespace Digitaria\RDMarketing\Block\Adminhtml\CreateFields;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Digitaria\RDMarketing\Service\Connect;
use Magento\Framework\HTTP\Client\Curl;

class Result extends Template
{
    protected $connect;
    protected $pageFactory;
    protected $curl;

    public function __construct(
        Context $context,
        Connect $connect,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        Curl $curl,
        array $data = []
    ) {
        $this->connect = $connect;
        $this->pageFactory = $pageFactory;
        $this->curl = $curl;
        parent::__construct($context, $data);
    }

    public function resultGenerateAction()
    {
        $fieldData = [
            [
                'api_identifier' => 'cf_order_id',
                'name' => ['pt-BR' => 'Número do Pedido'],
                'label' => ['pt-BR' => 'Número do Pedido']
            ],
            [
                'api_identifier' => 'cf_order_total_items',
                'name' => ['pt-BR' => 'Total de Itens do Pedido'],
                'label' => ['pt-BR' => 'Total de Itens do Pedido']
            ],
            [
                'api_identifier' => 'cf_order_status',
                'name' => ['pt-BR' => 'Status do Pedido'],
                'label' => ['pt-BR' => 'Status do Pedido']
            ],
            [
                'api_identifier' => 'cf_payment_method',
                'name' => ['pt-BR' => 'Método de Pagamento'],
                'label' => ['pt-BR' => 'Método de Pagamento']
            ],
            [
                'api_identifier' => 'cf_product_id',
                'name' => ['pt-BR' => 'ID do Produto'],
                'label' => ['pt-BR' => 'ID do Produto']
            ],
            [
                'api_identifier' => 'cf_product_sku',
                'name' => ['pt-BR' => 'SKU do Produto'],
                'label' => ['pt-BR' => 'SKU do Produto']
            ],
            [
                'api_identifier' => 'cf_order_date',
                'name' => ['pt-BR' => 'Data do Pedido'],
                'label' => ['pt-BR' => 'Data do Pedido']
            ],
            [
                'api_identifier' => 'cf_shipping_method',
                'name' => ['pt-BR' => 'Método de Entrega'],
                'label' => ['pt-BR' => 'Método de Entrega']
            ],
            [
                'api_identifier' => 'cf_coupon_code',
                'name' => ['pt-BR' => 'Cupom de Desconto Utilizado'],
                'label' => ['pt-BR' => 'Cupom de Desconto Utilizado']
            ],

            
        ];

        $responses = [];
        $getToken = $this->connect->getToken();
        $rdstationToken = $this->connect->getTokenFromDatabase();

        if ($this->connect->isTokenValid($rdstationToken)) {
            try {
                foreach ($fieldData as $data) {
                    $accessToken = $this->connect->getToken();
                    $endpoint = 'https://api.rd.services/platform/contacts/fields';
                    $headers = [
                        'accept' => 'application/json',
                        'authorization' => 'Bearer ' . $accessToken,
                        'content-type' => 'application/json',
                    ];
                    $requestData = [
                        'data_type' => 'STRING',
                        'name' => $data['name'],
                        'label' => $data['label'],
                        'presentation_type' => 'TEXT_INPUT',
                        'api_identifier' => $data['api_identifier']
                    ];
                    $this->curl->setHeaders($headers);
                    $this->curl->post($endpoint, json_encode($requestData));
                    $responseSuccess = $this->curl->getStatus() === 201; // Assuming status 201 indicates success

                    $responses[] = [
                        'success' => $responseSuccess,
                        'response_body' => $this->curl->getBody()
                    ];
                }
            } catch (\Exception $e) {
                $responses[] = [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            }
        } else {
            $responses[] = [
                'success' => false,
                'message' => 'Invalid token.'
            ];
        }

        return $responses;
    }

    function createErrorResponse($errorMessage)
    {
        $page = $this->pageFactory->create();
        $block = $page->getLayout()->getBlock('content');
        if ($block) {
            $block->setData('error_message', $errorMessage);
        }

        return $page;
    }

}