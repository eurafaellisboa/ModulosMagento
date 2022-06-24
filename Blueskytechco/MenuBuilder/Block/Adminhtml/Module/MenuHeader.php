<?php
/**
 * Copyright © 2021 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Blueskytechco\MenuBuilder\Block\Adminhtml\Module;

use Blueskytechco\MenuBuilder\Model\MenuBuilderFactory;

class MenuHeader extends \Magento\Backend\Block\Template
{
    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'menu_header.phtml';

    /**
     * AssignProducts constructor.
     *
     * @param \Magento\Backend\Block\Template\Context  $context
     * @param array                                    $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Blueskytechco\MenuBuilder\Model\MenuBuilderFactory $menuBuilderFactory,
        array $data = []
    ) {
        $this->menuBuilderFactory = $menuBuilderFactory;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve stores collection with default store
     */
    public function getDataContent()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $patternModel = $this->menuBuilderFactory->create()->load($id);
            return $patternModel;
        } else {
            return false;
        }
    }
}
