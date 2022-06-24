<?php

namespace Blueskytechco\Testimonial\Model;

class Testimonial extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'blueskytechco_testimonial';

    protected $_cacheTag = 'blueskytechco_testimonial';

    protected $_eventPrefix = 'blueskytechco_testimonial';

    protected function _construct()
    {
        $this->_init('Blueskytechco\Testimonial\Model\ResourceModel\Testimonial');
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