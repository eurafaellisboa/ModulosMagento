<?php

namespace MGS\Amp\Block\Page;


class AmpCmsPage extends AmpHome {
	
	protected function getCmsPageContent(){
		$csmPageContent = '';

		$csmPageContent = $this->_pageFactory->create()->load($this->getRequest()->getParam('page_id'))->getContent();

		
		return $csmPageContent;
	}
}