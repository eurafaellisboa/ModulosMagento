<?php

namespace Nwdthemes\Revslider\Setup;

class InstallData implements \Magento\Framework\Setup\InstallDataInterface {

    /**
     * @var \Nwdthemes\Revslider\Helper\Options
     */
    protected $_optionsHelper;

    /**
     * @param \Nwdthemes\Revslider\Helper\Options $optionsHelper
     */
    public function __construct(
        \Nwdthemes\Revslider\Helper\Options $optionsHelper
    ) {
        $this->_optionsHelper = $optionsHelper;
    }

    /**
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     */
    public function install(
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $options = [
            'date_format' => 'F j, Y',
            'time_format' => 'g:i a'
        ];
        foreach ($options as $handle => $value) {
            $this->_optionsHelper->updateOption($handle, $value);
        }
    }

}
