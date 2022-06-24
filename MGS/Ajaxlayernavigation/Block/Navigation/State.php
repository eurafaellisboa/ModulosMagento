<?php
namespace MGS\Ajaxlayernavigation\Block\Navigation;
 
class State extends \Magento\LayeredNavigation\Block\Navigation\State
{
    protected $_template = 'MGS_Ajaxlayernavigation::layer/state.phtml';

    public function getAppliedFilters()
    {
        $filters = $this->getLayer()->getState()->getFilters();
        if (!is_array($filters)) {
            $filters = [];
        }
        return $filters;
    }

    public function getClearUrl()
    {
        $filters = [];
        foreach ($this->getAppliedFilters() as $appliedFilter) {
            $filters[
                $appliedFilter->getFilter()->getRequestVar()
            ] = $appliedFilter->getFilter()->getCleanValue();
        }

        return $this->_urlBuilder->getUrl('*/*/*', [
                "_current" => true,
                "_use_rewrite" => true,
                "_escape" => true,
                "_query" => $filters,
            ]
        );
    }
 
}
