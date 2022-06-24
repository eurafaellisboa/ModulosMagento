<?php
 
namespace Blueskytechco\SetProduct\Block\Adminhtml\ProductSet;

use Magento\Backend\Block\Widget\Grid as WidgetGrid;
 
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;
 
    protected $_collection;
 
    /**
     * @var \Webkul\Grid\Model\Status
     */
    protected $_status;
    protected $_objectManager;
 
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
		\Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
		$this->_objectManager = $objectManager;
        parent::__construct($context, $backendHelper, $data);
    }
 
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('productsetGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(false);
        $this->setUseAjax(false);
    }

    protected function _prepareCollection() 
    {
		$collection = $this->_objectManager->create('Blueskytechco\SetProduct\Model\ProductSet', [])->getCollection(); 
		$this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }
 
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            [
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'entity_id'
            ]
        );

        $this->addColumn(
            'name',
            [
                'header' => __('Name'),
                'type' => 'text',
                'index' => 'name'
            ]
        );
		
		$this->addColumn(
            'title',
            [
                'header' => __('Title'),
                'type' => 'text',
                'index' => 'title'
            ]
        );

        $this->addColumn(
            'title_link',
            [
                'header' => __('Title Link'),
                'type' => 'text',
                'index' => 'title_link'
            ]
        );

        $this->addColumn(
            'button_style',
            [
                'header' => __('Button Style'),
                'type' => 'text',
                'index' => 'button_style'
            ]
        );

        $this->addColumn(
            'banner_image',
            [
                'header' => __('Images'),
                'renderer' => 'Blueskytechco\SetProduct\Block\Adminhtml\ProductSet\Renderer\Image',
                'filter' => false,
                'order' => false
            ]
        );
		
		$this->addColumn(
			'action',
			[
				'header' => __('Action'),
				'type' => 'action',
				'getter' => 'getId',
				'actions' => [
					[
						'caption' => __('Edit'),
						'url' => ['base' => 'addproductsset/productset/edit'],
						'field' => 'id',
					],
				], 
				'filter' => false,
				'sortable' => false,
				'header_css_class' => 'col-action',
				'column_css_class' => 'col-action',
			]
		);

        $this->addColumn(
            'delete',
            [
                'header' => '',
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('Delete'),
                        'url' => ['base' => 'addproductsset/productset/delete'],
                        'field' => 'id'
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
}