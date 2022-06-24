<?php

namespace Blueskytechco\Testimonial\Block\Adminhtml\Testimonial\Edit\Tab;

class Form extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    protected $_objectFactory;
    protected $_testimonial;
    protected $_systemStore;
    protected $_wysiwygConfig;
    protected $_reviewData = null;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\DataObjectFactory $objectFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Magento\Review\Helper\Data $reviewData,
        \Blueskytechco\Testimonial\Model\Testimonial $testimonial,
        array $data = []
    ) {
        $this->_objectFactory = $objectFactory;
        $this->_testimonial = $testimonial;
        $this->_systemStore = $systemStore;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_reviewData = $reviewData;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _prepareLayout()
    {
        $this->getLayout()->getBlock('page.title')->setPageTitle($this->getPageTitle());
        return $this;
    }

    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('testimonial');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('magic_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Testimonial Information')]);

        if ($model->getId()) {
            $fieldset->addField('testimonial_id', 'hidden', ['name' => 'testimonial_id']);
        }

        $fieldset->addField('name', 'text',
            [
                'label' => __('Author'),
                'title' => __('Author'),
                'name'  => 'name',
                'required' => true,
            ]
        );

        $fieldset->addField('image', 'image',
            [
                'label' => __('Avatar'),
                'title' => __('Avatar'),
                'name'  => 'image',
                'required' => true,
            ]
        );

        $fieldset->addField('text', 'editor',
            [
                'label' => __('Quote'),
                'title' => __('Quote'),
                'name'  => 'text',
                'required' => true,
            ]
        );

        $fieldset->addField('job', 'text',
            [
                'label' => __('Job'),
                'title' => __('Job'),
                'name' => 'job',
                'required' => true,
            ]
        );

        $summary = $this->getLayout()->createBlock('Blueskytechco\Testimonial\Block\Adminhtml\Helper\Renderer\Form\Summary');
        $fieldset->addField('summary_rating', 'note', array(
            'label'     => __('Summary Rating'),
            'text'      => $summary->ratingHtml(),
        ));

        $fieldset->addField('detailed_rating', 'note', array(
            'label'     => __('Detailed Rating'),
            'text'      => $summary->detailedHtml(),
        ));


        $fieldset->addField('order', 'text',
            [
                'label' => __('Order'),
                'title' => __('Order'),
                'name'  => 'order',
            ]
        );

        $form->addValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @return mixed
     */
    public function getTestimonial()
    {
        return $this->_coreRegistry->registry('testimonial');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getPageTitle()
    {
        return $this->getTestimonial()->getId()
            ? __("Edit Testimonial '%1'", $this->escapeHtml($this->getTestimonial()->getName())) : __('New Testimonial');
    }

    /**
     * Prepare label for tab.
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('General Information');
    }

    /**
     * Prepare title for tab.
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}
