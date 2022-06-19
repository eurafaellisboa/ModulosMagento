<?php

namespace Nwdthemes\Revslider\Observer;

use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\ScopeInterface;
use Nwdthemes\Revslider\Helper\Data;
use Nwdthemes\Revslider\Model\OptionFactory;

class LayoutLoadBefore implements ObserverInterface
{

    public function __construct(
        PageRepositoryInterface $pageRepository,
        Context $context,
        OptionFactory $optionFactory
    ) {
        $this->_optionFactory = $optionFactory;
        $this->_pageRepository = $pageRepository;
        $this->_request = $context->getRequest();
        $this->_status = $context->getScopeConfig()->getValue(
            'nwdthemes_revslider/revslider_configuration/status',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function execute(Observer $observer)
    {
        if ($this->_status) {

            $layoutUpdate = $observer->getData('layout')->getUpdate();

            $option = $this->_optionFactory
                ->create()
                ->getCollection()
                ->addFieldToFilter('handle', 'revslider-global-settings')
                ->setPageSize(1)
                ->getFirstItem()
                ->getData('option');
            $settings = json_decode($option, true);

            $includeSlider = ! isset($settings['allinclude']) || $settings['allinclude'] == 'true';
            if ( ! $includeSlider && isset($settings['includeids'])) {

                $pageHandles = $layoutUpdate->getHandles();
                if ($pageId = $this->_request->getParam('page_id', $this->_request->getParam('id', false))) {
                    try {
                        if ($page = $this->_pageRepository->getById($pageId)) {
                            $pageHandles[] = $page->getIdentifier();
                        }
                    } catch (\Exception $e) {}
                }

                $arrHandles = explode(',', $settings['includeids']);
                foreach ($arrHandles as $handle) {
                    $handle = trim($handle);
                    if (in_array($handle, $pageHandles) || in_array(str_replace('-', '_', $handle), $pageHandles)) {
                        $includeSlider = true;
                        continue;
                    }
                }
            }

            if ($includeSlider) {
                $layoutUpdate->addHandle('nwdthemes_revslider_default');
            }
        }
    }

}