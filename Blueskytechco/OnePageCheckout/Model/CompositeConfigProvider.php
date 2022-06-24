<?php
namespace Blueskytechco\OnePageCheckout\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\GiftMessage\Model\CompositeConfigProvider as GiftMessageConfig;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\LayoutInterface;
use Blueskytechco\OnePageCheckout\Model\ResourceModel\CompositeConfig;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\HTTP\Client\Curl;
use Psr\Log\LoggerInterface;

class CompositeConfigProvider implements ConfigProviderInterface
{
    /**
     * config helper.
     *
     * @var Config
     */
    private $configHelper;

    /**
     * @var GiftMessageConfig
     */
    private $configProvider;

    /**
     * @var Json
     */
    private $serializer;

    /**
     * @var LayoutInterface
     */
    private $layout;

    /**
     * @var CompositeConfig
     */
    private $compositeConfig;

    /**
     * @var RemoteAddress
     */
    private $remoteAddress;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Curl
     */
    private $curl;

    /**
     * CompositeConfigProvider constructor.
     * @param \Blueskytechco\OnePageCheckout\Helper\Data $configHelper
     * @param GiftMessageConfig $configProvider
     * @param Json $serializer
     * @param LayoutInterface $layout
     * @param CompositeConfig $compositeConfig
     * @param RemoteAddress $remoteAddress
     * @param Curl $curl
     * @param LoggerInterface $logger
     */
    public function __construct(
        \Blueskytechco\OnePageCheckout\Helper\Data $configHelper,
        GiftMessageConfig $configProvider,
        Json $serializer,
        LayoutInterface $layout,
        CompositeConfig $compositeConfig,
        RemoteAddress $remoteAddress,
        Curl $curl,
        LoggerInterface $logger
    ) {
        $this->configHelper = $configHelper;
        $this->configProvider = $configProvider;
        $this->serializer = $serializer;
        $this->layout = $layout;
        $this->compositeConfig = $compositeConfig;
        $this->remoteAddress = $remoteAddress;
        $this->curl = $curl;
        $this->logger = $logger;
    }

    /**
     * Get Config
     *
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getConfig()
    {
        $output = [];
        $helper = $this->configHelper;
        if ($helper->getModuleStatus()) {
            $config['googleApiAutoComplete'] = false;
            $config['googleApiListCountries'] = $this->compositeConfig->getCountryHasRegion();
            $config['autoCreateNewAccount']['enable'] = true;
            $config['autoCreateNewAccount']['minLength'] = 8;
            $config['autoCreateNewAccount']['minCharacterSets'] = 3;
            $config['titlePlaceOrder'] = $this->configHelper->getGeneral('title_place_order');
            if ($helper->isDisplayField('show_gift_message') && $helper->isMessagesAllowed()) {
                $config['giftOptionsConfig'] = $this->getGiftOptionsConfigJson();
            }
            $output['OnePageCheckout'] = $config;
            $output['paypal_in_context'] = false;
            $output['rewrite_email_element'] = true;
            $output['opcWidget'] = $this->getOpcWidget();
        }
        return $output;
    }

    /**
     * Get Widget
     *
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getOpcWidget()
    {
        $result = [];
        if($this->configHelper->isDisplayField('show_widget_box') && $this->configHelper->getGeneral('widget_after_placeorder'))
        {
            $result['widget_after_placeorder'][] =
                $this->layout->createBlock(\Magento\Cms\Block\Block::class)
                    ->setBlockId($this->configHelper->getGeneral('widget_after_placeorder'))->toHtml();
        }
        return $result;
    }
    /**
     * Retrieve gift message configuration
     *
     * @return string
     */
    private function getGiftOptionsConfigJson()
    {
        return $this->configProvider->getConfig();
    }
}
