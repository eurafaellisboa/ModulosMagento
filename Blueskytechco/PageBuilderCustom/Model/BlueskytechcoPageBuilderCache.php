<?php

namespace Blueskytechco\PageBuilderCustom\Model;

class BlueskytechcoPageBuilderCache extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'blueskytechco_page_builder_cache';

    protected $_cacheTag = 'blueskytechco_page_builder_cache';

    protected $_eventPrefix = 'blueskytechco_page_builder_cache';

    protected function _construct()
    {
        $this->_init('Blueskytechco\PageBuilderCustom\Model\ResourceModel\BlueskytechcoPageBuilderCache');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues()
    {
        $values = [];

        return $values;
    }
}