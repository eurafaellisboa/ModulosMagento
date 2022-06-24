<?php
namespace Blueskytechco\OnePageCheckout\Plugin\Checkout\Controller\Index;

use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Checkout\Controller\Index\Index as CheckoutIndex;
use Magento\Framework\UrlInterface;
class Index
{
    /**
     * @var RedirectFactory
     */
    private $resultRedirectFactory;

    /**
     * One step checkout helper
     *
     * @var Config
     */
    private $configHelper;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @param RedirectFactory $resultRedirectFactory
     * @param Config $configHelper
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        RedirectFactory $resultRedirectFactory,
        \Blueskytechco\OnePageCheckout\Helper\Data $configHelper,
        UrlInterface $urlBuilder
    ) {
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->configHelper = $configHelper;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @param CheckoutIndex $subject
     * @param callable $proceed
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundExecute(
        CheckoutIndex $subject,
        $proceed
    ) {
        if ($this->configHelper->getModuleStatus()) {
            $path = 'onepagecheckout';
            $router = $this->configHelper->getConfigUrl();
            if ($router) {
                $router = preg_replace('/\s+/', '', $router);
                $router = preg_replace('/\/+/', '', $router);
                $path = trim($router, '/');
            }
            $url = trim($this->urlBuilder->getUrl($path), '/');
            return $this->resultRedirectFactory->create()->setUrl($url);
        } else {
            return $proceed();
        }
    }
}
