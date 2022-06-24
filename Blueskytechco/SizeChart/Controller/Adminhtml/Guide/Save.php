<?php
namespace Blueskytechco\SizeChart\Controller\Adminhtml\Guide;

use Blueskytechco\SizeChart\Controller\Adminhtml\AbstractStore;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Save
 *
 * @package Blueskytechco\SizeChart\Controller\Adminhtml\Guide
 */
class Save extends AbstractStore
{
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            if (!$data['entity_id']) {
                unset($data['entity_id']);
            }
            if (isset($data['category_ids'])) {
                $data['category_ids'] = implode(',',$data['category_ids']);
            }
            $sizeChart = $this->sizeChartFactory->create();
            $sizeChart->setData($data);
            $this->_eventManager->dispatch(
                'size_chart_prepare_save',
                ['size_chart' => $sizeChart, 'request' => $this->getRequest()]
            );
            try {
                $this->sizeChartRepository->save($sizeChart);
                $this->messageManager->addSuccessMessage(__('The size chart has been saved.'));
                $this->_getSession()->setFormData(false);
                return $resultRedirect->setPath('*/*/edit', ['id' => $sizeChart->getId(), '_current' => true]);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the size chart.'));
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }
        return $resultRedirect->setPath('sizechart/guide');
    }
}
