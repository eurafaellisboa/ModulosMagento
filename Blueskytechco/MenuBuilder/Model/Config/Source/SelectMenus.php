<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Blueskytechco\MenuBuilder\Model\Config\Source;

class SelectMenus implements \Magento\Framework\Data\OptionSourceInterface
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
        $data_menus = $menus->getMenus();
        $array = [];
        foreach ($data_menus as $k => $item) {
            $array[] = ['value' => $item['identifier'], 'label' => $item['name']];
        }
        return $array;
    }
}
