<?php

namespace MGS\Guestwishlist\Controller\Index;

use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Framework\App\Action\Action {

    /**
     * Display guest wishlist
     *
     * @return \Magento\Framework\View\Result\Page
     * @throws NotFoundException
     */
    public function execute() {
        /** @var \Magento\Framework\View\Result\Page resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
		
		$pageTitle = __('My Wishlist');
		
		$breadcrumbs = $resultPage->getLayout()->getBlock('breadcrumbs');
		$breadcrumbs->addCrumb('home', [
			'label' => __('Home'),
			'title' => __('Home'),
			'link' => $this->_url->getUrl('')
				]
		);
		$breadcrumbs->addCrumb('mgs_guestwishlist', [
			'label' => __('My Wishlist'),
			'title' => __('My Wishlist')
				]
		);
		
		$resultPage->getConfig()->getTitle()->set($pageTitle);
		
        return $resultPage;
    }

}
