<?php
namespace Blueskytechco\OnePageCheckout\Controller\Adminhtml\Field;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Config\Model\ResourceModel\Config;
use Magento\Framework\App\Config\ReinitableConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Blueskytechco\OnePageCheckout\Helper\Data as OpcHelper;

/**
 * Class Save
 * @package Blueskytechco\OnePageCheckout\Controller\Adminhtml\Field
 */
class Save extends Action
{
    /**
     * @var Config
     */
    private $resourceConfig;

    /**
     * @var ScopeConfigInterface
     */
    private $_appConfig;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * Save constructor.
     *
     * @param Context $context
     * @param Config $resourceConfig
     * @param ReinitableConfigInterface $config
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        Config $resourceConfig,
        ReinitableConfigInterface $config,
        JsonFactory $resultJsonFactory
    ) {
        $this->resourceConfig    = $resourceConfig;
        $this->_appConfig        = $config;
        $this->resultJsonFactory = $resultJsonFactory;

        parent::__construct($context);
    }

    /**
     * Save position to config
     *
     * @return ResponseInterface|Json|ResultInterface
     */
    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();

        $result = [
            'message' => (string) __('Error during save field position.'),
            'type'    => 'error',
        ];

        $fields   = $this->getRequest()->getParam('fields', false);
        if ($fields) {
            try {
                $this->resourceConfig->saveConfig(
                    OpcHelper::XML_PATH_SORTED_FIELD_POSITION,
                    $fields,
                    ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                    0
                );
            } catch (Exception $e) {
                $result['message'] = $e->getMessage();

                return $resultJson->setData($result);
            }

            // re-init configuration
            $this->_appConfig->reinit();

            $result['message'] = (string) __('All fields have been saved.');
            $result['type']    = 'success';
        }

        return $resultJson->setData($result);
    }
}
