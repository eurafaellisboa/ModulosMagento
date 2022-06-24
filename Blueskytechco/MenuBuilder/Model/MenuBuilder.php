<?php
/**
 * Copyright © 2021 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Blueskytechco\MenuBuilder\Model;

use Magento\Framework\Model\AbstractModel;
use Blueskytechco\MenuBuilder\Model\ResourceModel\MenuBuilder as MenuBuilderResourceModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;

class MenuBuilder extends AbstractModel
{
    /**
     * Pattern constructor.
     * @param Context $context
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        Registry $registry
    ) {
        parent::__construct(
            $context,
            $registry
        );
    }

    /**
     * @return void
     */
    public function _construct()
    {
        $this->_init(MenuBuilderResourceModel::class);
    }
    
    /**
     * Create Menu Builder
     * @return \Blueskytechco\MenuBuilder\Model\MenuBuilder
     */
    public function createMenuBuilder($name, $type)
    {
        $this->setData([
            'name' => $name,
            'type' => $type,
        ])->setId(null)->save();
        return $this;
    }
}
