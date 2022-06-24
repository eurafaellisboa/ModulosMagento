<?php

declare(strict_types=1);

namespace Blueskytechco\Testimonial\Block\Widget;

use Magento\Widget\Block\BlockInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\App\Filesystem\DirectoryList;

class Testimonial extends Template  implements BlockInterface
{
    protected $_imageFactory;
    protected $testimonialFactory;
    protected $_testimonials;
    protected $_templateFilterContent;

    public function __construct(
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Image\AdapterFactory $imageFactory,
        \Blueskytechco\Testimonial\Model\TestimonialFactory $testimonialFactory,
        array $data = []
    ) {
        $this->_templateFilterContent = $filterProvider;
        $this->testimonialFactory = $testimonialFactory;
       $this->_imageFactory = $imageFactory;
        parent::__construct($context, $data);
    }

    public function _toHtml()
    {
        if($this->getDataWidgetConfig('template_id') && $this->getDataWidgetConfig('template_id') != 'default'){
            $this->setTemplate(
               'Blueskytechco_Testimonial::widget/'.$this->getDataWidgetConfig('template_id').'.phtml'
            );
        }
        $html = parent::_toHtml();
        return $html;
    }

    public function getTestimonials()
    {
        if(!$this->_testimonials){
            $testimonial_id = explode(',', $this->getDataWidgetConfig('testimonial_id'));
            $testimonials = $this->testimonialFactory->create()->getCollection();
            $testimonials->addFieldToFilter('testimonial_id', ['in'=>$testimonial_id]);
            $testimonials->getSelect()->order("find_in_set(testimonial_id,'".implode(',',$testimonial_id)."')");
            $this->_testimonials = $testimonials;
        }

        return $this->_testimonials;
    }

    public function getImage($object)
    {
        $resizedURL = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $object->getImage();
        return $resizedURL;
    }

    public function getDataWidgetConfig($path)
    {
        return $this->getData($path) ?: '';
    }

    public function filterOutputContent($content)
    {
        $content = (string) $content ?: '';
        if($content != ''){
            $arr_encode = ['^[','^]','`','|','&lt;','&gt;'];
            $arr_decode = ['{','}','"','\\','<','>'];
            $new_content = str_replace($arr_encode, $arr_decode, $content);
            return $this->_templateFilterContent->getPageFilter()->filter(
                (string) $new_content ?: ''
            );
        }
        return '';
    }
}