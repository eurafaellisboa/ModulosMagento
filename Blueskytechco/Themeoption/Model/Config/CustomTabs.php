<?php
namespace Blueskytechco\Themeoption\Model\Config;

class CustomTabs extends \Magento\Framework\App\Config\Value
{
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }
    
    protected function _afterLoad()
    {
        $get_value = $this->getValue();
        $_array = unserialize($get_value);
        $this->setValue($_array);
    }

    public function beforeSave()
    {
        $get_value = $this->getValue();
        unset($get_value['__empty']);
        $_array = serialize($get_value);
        
        $this->setValue($_array);
    }
}
