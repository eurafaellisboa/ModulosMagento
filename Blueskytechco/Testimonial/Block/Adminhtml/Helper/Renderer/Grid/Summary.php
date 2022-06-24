<?php
namespace Blueskytechco\Testimonial\Block\Adminhtml\Helper\Renderer\Grid; 

class Summary extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{

    protected $_storeManager;
    protected $_testimonialFactory;

    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Blueskytechco\Testimonial\Model\TestimonialFactory $testimonialFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_storeManager = $storeManager;
        $this->_testimonialFactory  = $testimonialFactory;
    }

    public function render(\Magento\Framework\DataObject $row)
    {
        $html = '<div class="field-summary_rating"><span class="rating-box" style="display:block;">';
        $html .= '<span style="display:block; width:'. $row->getData($this->getColumn()->getIndex()) * 20 .'%;" class="rating"></span>';
        $html .= '</span></div>';
        return $html;
    }
}
