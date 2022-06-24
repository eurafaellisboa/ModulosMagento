<?php
namespace Blueskytechco\Testimonial\Block\Adminhtml\Helper\Renderer\Grid;

class Image extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
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
        $storeViewId = $this->getRequest()->getParam('store');
        $testimonial = $this->_testimonialFactory->create()->setStoreViewId($storeViewId)->load($row->getId());
        $srcImage = $this->_storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            ) . $testimonial->getImage();

        return '<image style="max-width:50px;" src ="'.$srcImage.'" alt="'.$testimonial->getImage().'" >';
    }
}
