<?php
/**
 * Copyright © 2021 Blueskytechco. All rights reserved.
 */
namespace Blueskytechco\SizeChart\Model;

class SizeChartList extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    protected  $_sizechart;
    
    /**
     * 
     * @param \Blueskytechco\SizeChart\Model\SizeChart $sizechart
     */
    public function __construct(
        \Blueskytechco\SizeChart\Model\SizeChart $sizechart
        ) {
        $this->_sizechart = $sizechart;
    }
    
    
    /**
     *
     * @return array
     */
    public function getAvailableTemplate()
    {
        $sizecharts = $this->_sizechart->getCollection();
        $listSizechart = array();
        foreach ($sizecharts as $sizechart) {
            $listSizechart[] = array('label' => $sizechart->getName(),
                'value' => $sizechart->getId());
        }
        return $listSizechart;
    }

    /**
     * Get model option as array
     *
     * @return array
     */
    public function getAllOptions($withEmpty = true)
    {
        $options = array();
        $options = $this->getAvailableTemplate();

        if ($withEmpty) {
            array_unshift($options, array(
                'value' => '',
                'label' => '-- Please Select --',
                ));
        }
        return $options;
    }
}