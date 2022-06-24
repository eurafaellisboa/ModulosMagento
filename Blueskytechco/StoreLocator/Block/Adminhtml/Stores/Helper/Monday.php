<?php
/**
 * Copyright © 2019 Blueskytechco. All rights reserved.
 */

namespace Blueskytechco\StoreLocator\Block\Adminhtml\Stores\Helper;

use \Magento\Framework\Data\Form\Element\AbstractElement;
use \Magento\Framework\Data\Form\Element\Factory;
use \Magento\Framework\Data\Form\Element\CollectionFactory;
use \Magento\Framework\Escaper;
use \Blueskytechco\StoreLocator\Helper\Config as ConfigHelper;


class Monday extends AbstractElement
{
    /**
     * @var \Blueskytechco\StoreLocator\Helper\Config
     */
    private $configHelper;
	private $timeHours;

    /**
     * @param Factory              $factoryElement
     * @param CollectionFactory    $factoryCollection
     * @param Escaper              $escaper
     * @param ConfigHelper         $configHelper
     * @param array                $data
     */
    public function __construct(
        Factory $factoryElement,
        CollectionFactory $factoryCollection,
        Escaper $escaper,
        ConfigHelper $configHelper,
        array $data = []
    ) {
        $this->configHelper = $configHelper;
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
    }

    /**
     * Return the element as HTML
     *
     * @return string
     */
    public function getElementHtml() 
    {
		$html = '';
		$html .= '<div class="admin__field-time monday">';
        $html .= '<div class="admin__field-control"><select class="admin__control-select admin__control-select-time admin__select-time " name="time[monday_time]"><option data-title="Closed" value="0">'.__('Closed').'</option><option data-title="Open" value="1">'.__('Open').'</option></select></div>';
		$html .= '<div class="admin__field-control-to"><div class="admin__field-control"><span>'.__('Open Time').'</span><select class="admin__control-select admin__control-select-time from-hours" name="time[monday][from][hours]" >';
		for($i=0;$i<=11;$i++){
			if($i<10){
				$html .='<option data-title="'.$i.'" value="'.$i.'">0'.$i.'</option>';
			}else{
				$html .='<option data-title="'.$i.'" value="'.$i.'">'.$i.'</option>';
			}
		}
		$html .= '</select>';
		$html .= '<select class="admin__control-select admin__control-select-time from-minutes" name="time[monday][from][minutes]">';
		for($j=0;$j<60;$j++){
			if($j<10){
				$html .='<option data-title="'.$j.'" value="'.$j.'">0'.$j.'</option>';
			}else{
				$html .='<option data-title="'.$j.'" value="'.$j.'">'.$j.'</option>';
			}
		}
		$html .= '</select>';
		$html .= '</div>';
		$html .= '<div class="admin__field-control"><span>'.__('Closed Time').'</span><select class="admin__control-select admin__control-select-time to-hours" name="time[monday][to][hours]" >';
		for($z=0;$z<=11;$z++){
			if($z<10){
				$html .='<option data-title="'.$z.'" value="'.$z.'">0'.$z.'</option>';
			}else{
				$html .='<option data-title="'.$z.'" value="'.$z.'">'.$z.'</option>';
			}
		}
		$html .= '</select>';
		$html .= '<select class="admin__control-select admin__control-select-time to-minutes" name="time[monday][to][minutes]">';
		for($k=0;$k<60;$k++){
			if($k<10){
				$html .='<option data-title="'.$k.'" value="'.$k.'">0'.$k.'</option>';
			}else{
				$html .='<option data-title="'.$k.'" value="'.$k.'">'.$k.'</option>';
			}
		}
		$html .= '</select>';
		$html .= '</div></div></div>';
		$html .= $this->getAfterElementHtml();

        return $html;
    }
}
