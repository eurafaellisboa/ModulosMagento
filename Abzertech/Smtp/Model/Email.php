<?php

namespace Abzertech\Smtp\Model;

use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Mail\Template\Factory;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Abzertech\Smtp\Helper\Data;

class Email
{

    const XML_PATH_EMAIL_TEMPLATE_ZEND_TEST = 'abzer/smtp/zend_email_template';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * @var Factory
     */
    protected $templateFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var array
     */
    private $templateVars = [];

    /**
     * @var array
     */
    private $templateOptions = [];

    /**
     * @var string
     */
    private $templateModel;

    /**
     * @var StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * Email constructor.
     *
     * @param Data $dataHelper
     * @param Factory $templateFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param StateInterface $inlineTranslation
     * @param TransportBuilder $transportBuilder
     */
    public function __construct(
        Data $dataHelper,
        Factory $templateFactory,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        StateInterface $inlineTranslation,
        TransportBuilder $transportBuilder
    ) {
        $this->dataHelper = $dataHelper;
        $this->templateFactory = $templateFactory;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
    }

    /**
     * Generate Template
     *
     * @param Mixed $senderInfo
     * @param Mixed $receiverInfo
     * @return $this
     * @throws NoSuchEntityException
     */
    public function generateTemplate($senderInfo, $receiverInfo)
    {
        $this->getTransportBuilder()
                ->setTemplateIdentifier($templateId)
                ->setTemplateOptions(
                    [
                            'area' => Area::AREA_ADMINHTML,
                            'store' => $this->getStore()->getId(),
                        ]
                )
                ->setTemplateVars($this->templateVars)
                ->setFrom($senderInfo)
                ->addTo($receiverInfo['email'], $receiverInfo['name']);

        return $this;
    }

    /**
     * send
     *
     * @param $senderInfo
     * @param $receiverInfo
     * @throws MailException
     */
    public function send($senderInfo, $receiverInfo)
    {
        $this->inlineTranslation->suspend();
        $this->generateTemplate($senderInfo, $receiverInfo);
        $transport = $this->transportBuilder->getTransport();
        $result = $transport->sendMessage();
        $this->inlineTranslation->resume();

        return $result;
    }

    /**
     * Return Template
     *
     * @return $this
     */
    protected function getTemplate()
    {
        $this->setTemplateOptions(
            [
                    'area' => Area::AREA_ADMINHTML,
                    'store' => $this->getStore()->getId(),
                ]
        );

        $templateIdentifier = $this->getTemplateId(self::XML_PATH_EMAIL_TEMPLATE_ZEND_TEST);

        return $this->templateFactory->get($templateIdentifier, $this->templateModel)
                        ->setVars($this->templateVars)
                        ->setOptions($this->templateOptions);
    }

    /**
     * Return Email Body
     *
     * @return mixed
     */
    public function getEmailBody()
    {
        return $this->getTemplate()->processTemplate();
    }

    /**
     * Return template id according to store
     *
     * @param $xmlPath
     * @return mixed
     */
    public function getTemplateId($xmlPath)
    {
        return $this->getConfigValue($xmlPath, $this->getStore()->getStoreId());
    }

    /**
     * Return store configuration value
     *
     * @param string $path
     * @param int $storeId
     * @return mixed
     */
    protected function getConfigValue($path, $storeId)
    {
        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Return store
     *
     * @return StoreInterface
     */
    public function getStore()
    {
        return $this->storeManager->getStore();
    }

    /**
     * set store templates variables
     *
     * @param array $templateVars
     * @return $this
     */
    public function setTemplateVars($templateVars)
    {
        $this->templateVars = (array) $templateVars;
        return $this;
    }

    /**
     * set store templates options
     *
     * @param mixed $templateOptions
     * @return $this
     */
    public function setTemplateOptions($templateOptions)
    {
        $this->templateOptions = (array) $templateOptions;
        return $this;
    }

    /**
     * set store templates model
     *
     * @param mixed $templateModel
     * @return $this
     */
    public function setTemplateModel($templateModel)
    {
        $this->templateModel = $templateModel;
        return $this;
    }

    /**
     * Return Transport Builder
     *
     * @return transportBuilder
     */
    public function getTransportBuilder()
    {
        return $this->transportBuilder;
    }
}
