<?php
namespace MGS\Amp\Plugin\Review;

use Magento\Review\Block\Form as MgtForm;

class Form {
    /**
     * @var \MGS\Amp\Helper\Config
     */
    protected $_configHelper;

	/**
     * Path to AMP-template file.
     *
     * @var string
     */
    protected $_template = 'MGS_Amp::Magento_Review/form.phtml';
	
    /**
     * @param \MGS\Amp\Helper\Config $configHelper
     * @return  void
     */
    public function __construct(
        \MGS\Amp\Helper\Config $configHelper
    ) {
        $this->_configHelper = $configHelper;
    }
	
	/**
     * @param  MgtForm
     * @param  string $result
     * @return string $result
     */
    public function afterGetTemplate(MgtForm $subject, $result)
    {
        if (!$this->_configHelper->isAmpCall()){
            return $result;
        }

        return $this->_template;
    }

}
