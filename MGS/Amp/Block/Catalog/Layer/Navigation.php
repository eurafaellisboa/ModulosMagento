<?php
namespace MGS\Amp\Block\Catalog\Layer;

class Navigation extends \Magento\LayeredNavigation\Block\Navigation
{
    /**
     * Apply layer
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->renderer = $this->getChildBlock('render');
        foreach ($this->filterList->getFilters($this->_catalogLayer) as $filter) {
            $filter->apply($this->getRequest());
        }
        $this->getLayer()->apply();
        return \Magento\Framework\View\Element\Template::_prepareLayout();
    }

}