<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
 
namespace MGS\Fbuilder\Controller\Index;

use Magento\Framework\Controller\ResultFactory;
class Ajax extends \Magento\Framework\App\Action\Action
{
	/**
     * @var Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;
	/**
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
 
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        parent::__construct($context);
 
    }
	
    public function execute()
    {
		if($this->getRequest()->isAjax()){
			$data = $this->getRequest()->getParams();
			unset($data['_']);
			
			$resultPage = $this->_resultPageFactory->create();
			if($data['category_id']=='' || is_null($data['category_id'])){
				$data['category_ids'] = $data['block_type'];
				// Product Tabs block
				switch ($data['attribute_type']){
					case "mgs_new_products_tabs":
						$blockClass = 'MGS\Fbuilder\Block\Products\NewProducts';
						$type = 'new';
						$attribute = NULL;
						break;
					case "mgs_sale_products_tabs":
						$blockClass = 'MGS\Fbuilder\Block\Products\Sale';
						$type = 'sale';
						$attribute = NULL;
						break;
					case "mgs_rate_products_tabs":
						$blockClass = 'MGS\Fbuilder\Block\Products\Rate';
						$type = 'rate';
						$attribute = NULL;
						break;
					default:
						$blockClass = 'MGS\Fbuilder\Block\Products\Attributes';
						$type = $attribute = $data['attribute_type'];
						break;
				}
				
				$html = $resultPage->getLayout()
						->createBlock($blockClass)
						->setData($data)
						->setAttribute($attribute)
						->setTabAttribute($attribute)
						->setTabType($type)
						->setLimit($data['limit'])
						->setIsAjax(1)
						->setTemplate('MGS_Fbuilder::products/tabs/items.phtml')
						->toHtml();
			}elseif($data['attribute_type']==''){
				switch ($data['block_type']){
					case "new":
						$blockClass = 'MGS\Fbuilder\Block\Products\NewProducts';
						$type = 'new';
						$attribute = NULL;
						break;
					case "sale":
						$blockClass = 'MGS\Fbuilder\Block\Products\Sale';
						$type = 'sale';
						$attribute = NULL;
						break;
					case "rate":
						$blockClass = 'MGS\Fbuilder\Block\Products\Rate';
						$type = 'rate';
						$attribute = NULL;
						break;
				}

				$html = $resultPage->getLayout()
					->createBlock($blockClass)
					->setBlockData($data)
					->setAttribute($attribute)
					->setTabAttribute($attribute)
					->setTabType($type)
					->setLimit($data['limit'])
					->setIsAjax(1)
					->setTemplate('products/ajax.phtml')
					->toHtml();
			}else{
				// Product blocks with category tabs
				$html = $resultPage->getLayout()
					->createBlock('MGS\Fbuilder\Block\Products\Category')
					->setBlockData($data)
					->setLimit($data['limit'])
					->setIsAjax(1)
					->setTemplate('products/ajax.phtml')
					->toHtml();
			}
			return $this->getResponse()->setBody($html);
		}else{
			$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
			$resultRedirect->setUrl($this->_redirect->getRefererUrl());
			return $resultRedirect;
		}
    }
}