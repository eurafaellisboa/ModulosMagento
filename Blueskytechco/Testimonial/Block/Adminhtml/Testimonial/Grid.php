<?php

namespace Blueskytechco\Testimonial\Block\Adminhtml\Testimonial;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    protected $_reviewData = null;

    protected $_testimonialCollectionFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Review\Helper\Data $reviewData,
        \Blueskytechco\Testimonial\Model\ResourceModel\Testimonial\CollectionFactory $testimonialCollectionFactory,
    
        array $data = []
    ) {

        $this->_reviewData = $reviewData;
        $this->_testimonialCollectionFactory = $testimonialCollectionFactory;

        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('testimonialGrid');
        $this->setDefaultSort('testimonial_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = $this->_testimonialCollectionFactory->create();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'testimonial_id',
            [
                'header' => __('Testimonial ID'),
                'type' => 'number',
                'index' => 'testimonial_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
            ]
        );

        $this->addColumn(
            'name',
            [
                'header' => __('Author'),
                'type' => 'text',
                'index' => 'name',
                'header_css_class' => 'col-name',
                'column_css_class' => 'col-name',
            ]
        );

         $this->addColumn(
            'job',
            [
                'header' => __('Job'),
                'type' => 'text',
                'index' => 'job',
                'header_css_class' => 'col-job',
                'column_css_class' => 'col-job',
            ]
        );

        $this->addColumn(
            'image',
            [
                'header' => __('Avatar'),
                'class' => 'xxx',
                'width' => '50px',
                'filter' => false,
                'renderer' => 'Blueskytechco\Testimonial\Block\Adminhtml\Helper\Renderer\Grid\Image',
            ]
        );

        $this->addColumn('rating_summary', array(
            'header'    => __('Rating'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'rating_summary',
            'type'      => 'options',
            'renderer'  => 'Blueskytechco\Testimonial\Block\Adminhtml\Helper\Renderer\Grid\Summary',
            'options'   => array(
              1 => '1 star',
              2 => '2 stars',
              3 => '3 stars',
              4 => '4 stars',
              5 => '5 stars',
            ),
        ));

        $this->addColumn(
            'order',
            [
                'header' => __('Order'),
                'type' => 'text',
                'index' => 'order',
                'header_css_class' => 'col-order',
                'column_css_class' => 'col-order',
            ]
        );

        $this->addColumn(
            'edit',
            [
                'header' => __('Edit'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('Edit'),
                        'url' => ['base' => '*/*/edit'],
                        'field' => 'testimonial_id',
                    ],
                ],
                'filter' => false,
                'sortable' => false,
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action',
            ]
        );

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('testimonial_id');
        $this->getMassactionBlock()->setFormFieldName('testimonial');

        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl('testimonial/*/massDelete'),
                'confirm' => __('Are you sure?'),
            ]
        );

        return $this;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }

    public function getRowUrl($row)
    {
        return $this->getUrl(
            '*/*/edit',
            ['testimonial_id' => $row->getId()]
        );
    }
}
