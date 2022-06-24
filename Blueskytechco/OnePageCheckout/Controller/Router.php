<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Blueskytechco\OnePageCheckout\Controller;

use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\Response\Http as HttpResponse;
use Magento\UrlRewrite\Controller\Adminhtml\Url\Rewrite;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\Action\Redirect;
use Magento\Framework\App\ActionInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Router implements \Magento\Framework\App\RouterInterface
{
    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $actionFactory;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_helper;

    /**
     * Router constructor.
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     * @param \Blueskytechco\OnePageCheckout\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Blueskytechco\OnePageCheckout\Helper\Data $helper
    ) {
        $this->_helper = $helper;
        $this->actionFactory = $actionFactory;
    }

    /**
     * Match corresponding URL Rewrite and modify request
     *
     * @param \Magento\Framework\App\RequestInterface|HttpRequest $request
     * @return ActionInterface|null
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        $identifier = trim($request->getPathInfo(), '/');
        if(!$this->_helper->getModuleStatus() || $this->_helper->getConfigUrl() != $identifier)
            return null;
        $moduleFrontName = 'Blueskytechco_OnePageCheckout';
        $actionPath = 'index';
        $action = 'index';
        $currentModuleName = 'Blueskytechco_OnePageCheckout';
        $routeName = 'onepagecheckout';
        $request->setModuleName($moduleFrontName);
        $request->setControllerName($actionPath);
        $request->setActionName($action);
        $request->setControllerModule($currentModuleName);
        $request->setRouteName($routeName);
        return $this->actionFactory->create(\Magento\Framework\App\Action\Forward::class);
    }    /**
     * @param \Magento\Framework\App\RequestInterface|HttpRequest $request
     * @param string  $url
     * @param integer $code
     * @return ActionInterface
     */
    protected function redirect($request, $url, $code)
    {
        $this->response->setRedirect($url, $code);
        $request->setDispatched(true);
        return $this->actionFactory->create(Redirect::class);
    }

}
