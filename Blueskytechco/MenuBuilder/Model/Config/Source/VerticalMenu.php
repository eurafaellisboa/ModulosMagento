<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Blueskytechco\MenuBuilder\Model\Config\Source;

class VerticalMenu implements \Magento\Framework\Data\OptionSourceInterface
{
    
    /**
     * @var \Blueskytechco\MenuBuilder\Model\MenuBuilderFactory
     */
    protected $menuBuilderFactory;
    
    public function __construct(
        \Blueskytechco\MenuBuilder\Model\MenuBuilderFactory $menuBuilderFactory
    ) {
        $this->menuBuilderFactory = $menuBuilderFactory;
    }
    
    public function toOptionArray()
    {
        $menus = $this->menuBuilderFactory->create()->getResource();
        $data_menu_vertical = $menus->getVerticalMenu();
        $array = [];
        foreach ($data_menu_vertical as $k => $item) {
            $array[] = ['value' => $item['identifier'], 'label' => $item['name']];
        }
        return $array;
    }
}
