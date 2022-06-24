<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Blueskytechco\SearchSuite\Controller\Search\Ajax;

use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Search\Model\AutocompleteInterface;
use Magento\Framework\Controller\ResultFactory;
use \Blueskytechco\SearchSuite\Model\Search as SearchModel;

class Suggest extends Action implements HttpGetActionInterface
{
	/**
     * @var  \Magento\Search\Model\AutocompleteInterface
     */
    private $autocomplete;
	
	/**
     * @var \Blueskytechco\SearchSuite\Model\Search
     */
    private $searchModel;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Search\Model\AutocompleteInterface $autocomplete
     */
    public function __construct(
        Context $context,
		SearchModel $searchModel,
        AutocompleteInterface $autocomplete
    ) {
        $this->autocomplete = $autocomplete;
		$this->searchModel = $searchModel;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if (!$this->getRequest()->getParam('q', false)) {
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($this->_url->getBaseUrl());
            return $resultRedirect;
        }

        $responseData = [];
		
		$responseData['result'] = $this->searchModel->getData();

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($responseData);
        return $resultJson;
    }
}
