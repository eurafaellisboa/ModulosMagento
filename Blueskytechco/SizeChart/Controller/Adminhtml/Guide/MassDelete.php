<?php
namespace Blueskytechco\SizeChart\Controller\Adminhtml\Guide;

use Blueskytechco\SizeChart\Controller\Adminhtml\AbstractStore;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Save
 *
 * @package Blueskytechco\SizeChart\Controller\Adminhtml\Guide
 */
class MassDelete extends AbstractStore
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionSizeChartFactory->create());

		$collectionSize = $collection->getSize();
		$delete = 0;
        foreach ($collection as $item) {
            try {
				$this->deleteItem($item);
				$this->messageManager->addSuccessMessage(
					__('A total of %1 record(s) have been deleted.', $collectionSize)
				);
			} catch (\Exception $e) {
				$this->logger->critical($e);
				$this->messageManager->addErrorMessage($e->getMessage());
			}
            $delete++;
        }

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('sizechart/guide');
    }
	
	protected function deleteItem($item)
    {
        return $item->delete();
    }
}
