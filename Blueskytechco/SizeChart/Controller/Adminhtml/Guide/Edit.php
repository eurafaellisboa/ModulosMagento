<?php
namespace Blueskytechco\SizeChart\Controller\Adminhtml\Guide;

use Blueskytechco\SizeChart\Controller\Adminhtml\AbstractStore;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Save
 *
 * @package Blueskytechco\SizeChart\Controller\Adminhtml\Guide
 */
class Edit extends AbstractStore
{
    /**
     *
     * @return mixed
     */
    public function execute() 
    {
        $patternId = $this->getRequest()->getParam('id');
		$pattern = $this->sizeChartFactory->create();
        if ($patternId) {
            try {
                $pattern = $this->sizeChartFactory->create()->load($patternId);
                $pageTitle = sprintf("%s", $pattern->getName());
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('This pattern no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                return $resultRedirect->setPath('sizechart/suide');
            }
        } else {
            $pageTitle = __('New Size Chart');
        }
		$this->registry->register('size_chart', $pattern);
        $breadcrumb = $patternId ? __('Edit Size Chart') : __('New Size Chart');
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Blueskytechco_SizeChart::sizechart');
        $resultPage->addBreadcrumb($breadcrumb, $breadcrumb);
        $resultPage->getConfig()->getTitle()->prepend(__('Size Chart'));
        $resultPage->getConfig()->getTitle()->prepend($pageTitle);
        return $resultPage;
    }
}
