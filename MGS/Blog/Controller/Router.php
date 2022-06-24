<?php

namespace MGS\Blog\Controller;

use Magento\Framework\App\RouterInterface;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Url;
use MGS\Blog\Model\Post;
use MGS\Blog\Model\Category;
use MGS\Blog\Helper\Data;

class Router implements RouterInterface
{
    protected $actionFactory;
    protected $eventManager;
    protected $response;
    protected $dispatched;
    protected $postCollection;
    protected $categoryCollection;
    protected $blogHelper;
    protected $storeManager;

    public function __construct(
        ActionFactory $actionFactory,
        ResponseInterface $response,
        ManagerInterface $eventManager,
        Category $categoryCollection,
        Post $postCollection,
        Data $blogHelper,
        StoreManagerInterface $storeManager
    )
    {
        $this->actionFactory = $actionFactory;
        $this->eventManager = $eventManager;
        $this->response = $response;
        $this->blogHelper = $blogHelper;
        $this->categoryCollection = $categoryCollection;
        $this->postCollection = $postCollection;
        $this->storeManager = $storeManager;
    }

    public function match(RequestInterface $request)
    {
        $blogHelper = $this->blogHelper;
        if (!$this->dispatched) {
            $route = $blogHelper->getConfig('general_settings/route');
            $urlKey = trim($request->getPathInfo(), '/');
            $origUrlKey = $urlKey;
            $condition = new DataObject(['url_key' => $urlKey, 'continue' => true]);
            $this->eventManager->dispatch(
                'mgs_blog_controller_router_match_before',
                ['router' => $this, 'condition' => $condition]
            );
            $urlKey = $condition->getUrlKey();
            if ($condition->getRedirectUrl()) {
                $this->response->setRedirect($condition->getRedirectUrl());
                $request->setDispatched(true);
                return $this->actionFactory->create(
                    'Magento\Framework\App\Action\Redirect',
                    ['request' => $request]
                );
            }
            if (!$condition->getContinue()) {
                return null;
            }
            if ($urlKey == $route) {
                $request->setModuleName('blog')
                    ->setControllerName('index')
                    ->setActionName('index');
                $request->setAlias(Url::REWRITE_REQUEST_PATH_ALIAS, $urlKey);
                $this->dispatched = true;
                return $this->actionFactory->create(
                    'Magento\Framework\App\Action\Forward',
                    ['request' => $request]
                );
            }
            $identifiers = explode('/', $urlKey);
			if(isset($identifiers[0]) && $identifiers[0] == $route){
				if (count($identifiers) == 2) {
					$identifier = $identifiers[1];
					$category = $this->categoryCollection->getCollection()
						->addFieldToFilter('status', array('eq' => 1))
						->addFieldToFilter('url_key', array('eq' => $identifier))
						->addStoreFilter($this->storeManager->getStore()->getId())
						->getFirstItem();
					if ($category && $category->getId()) {
						$request->setModuleName('blog')
							->setControllerName('category')
							->setActionName('view')
							->setParam('category_id', $category->getId());
						$request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $origUrlKey);
						$request->setDispatched(true);
						$this->dispatched = true;
						return $this->actionFactory->create(
							'Magento\Framework\App\Action\Forward',
							['request' => $request]
						);
					}
					$post = $this->postCollection->getCollection()
						->addFieldToFilter('status', array('eq' => 1))
						->addFieldToFilter('url_key', array('eq' => $identifier))
						->addStoreFilter($this->storeManager->getStore()->getId())
						->getFirstItem();
					if ($post && $post->getId()) {
						$request->setModuleName('blog')
							->setControllerName('post')
							->setActionName('view')
							->setParam('post_id', $post->getId());
						$request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $origUrlKey);
						$request->setDispatched(true);
						$this->dispatched = true;
						return $this->actionFactory->create(
							'Magento\Framework\App\Action\Forward',
							['request' => $request]
						);
					}
				}
				if (count($identifiers) == 3) {
					$identifier = $identifiers[2];
					$post = $this->postCollection->getCollection()
						->addFieldToFilter('status', array('eq' => 1))
						->addFieldToFilter('url_key', array('eq' => $identifier))
						->addStoreFilter($this->storeManager->getStore()->getId())
						->getFirstItem();
					if ($post && $post->getId()) {
						$request->setModuleName('blog')
							->setControllerName('post')
							->setActionName('view')
							->setParam('post_id', $post->getId());
						$request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $origUrlKey);
						$request->setDispatched(true);
						$this->dispatched = true;
						return $this->actionFactory->create(
							'Magento\Framework\App\Action\Forward',
							['request' => $request]
						);
					}
				}
				$identifier = substr_replace($request->getPathInfo(), '', 0, strlen('/' . $route . '/'));
				if (substr($identifier, 0, strlen('tag/')) == 'tag/') {
					$identifier = substr_replace($identifier, '', 0, 4);
					if ($identifier != null || $identifier != '') {
						$request->setModuleName('blog')
							->setControllerName('tag')
							->setActionName('view')
							->setParam('tag', urldecode($identifier));
						$request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $origUrlKey);
						$request->setDispatched(true);
						$this->dispatched = true;
						return $this->actionFactory->create(
							'Magento\Framework\App\Action\Forward',
							['request' => $request]
						);
					}
				}
			}
        }
    }
}
