<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2016-01-05 10:40:51
 * @@Modify Date: 2019-01-25 17:13:48
 * @@Function:
 */

namespace Magiccart\Alothemes\Block\Adminhtml\Export;

use Magento\Theme\Model\Theme\Collection;
use Magento\Framework\App\Area;

class Magicmenu extends \Magiccart\Magicmenu\Block\Adminhtml\Extra\Grid
{

    protected function _prepareColumns()
    {
        parent::_prepareColumns();
        // $this->removeColumn('status');
        $this->removeColumn('edit');
        $this->_exportTypes = [];
        // $this->addExportType('*/*/menu', __('XML'));
    }

    // protected function _prepareMassaction()
    // {
    //     $this->setMassactionIdField('entity_id');
    //     $this->getMassactionBlock()->setFormFieldName('magicmenu');

    //     $this->getMassactionBlock()->addItem(
    //         'delete',
    //         [
    //             'label' => __('Delete'),
    //             'url' => $this->getUrl('magicmenu/*/massDelete'),
    //             'confirm' => __('Are you sure?'),
    //         ]
    //     );

    //     $statuses = Status::getAvailableStatuses();

    //     array_unshift($statuses, ['label' => '', 'value' => '']);
    //     $this->getMassactionBlock()->addItem(
    //         'status',
    //         [
    //             'label' => __('Change status'),
    //             'url' => $this->getUrl('magicmenu/*/massStatus', ['_current' => true]),
    //             'additional' => [
    //                 'visibility' => [
    //                     'name' => 'status',
    //                     'type' => 'select',
    //                     'class' => 'required-entry',
    //                     'label' => __('Status'),
    //                     'values' => $statuses,
    //                 ],
    //             ],
    //         ]
    //     );

    //     return $this;
    // }

	protected function _prepareMassaction()
	{
		$this->setMassactionIdField('magicmenu_id');
		$this->getMassactionBlock()->setFormFieldName('exportIds');
		// $stores = \Magento\Framework\App\ObjectManager::getInstance()->get(
  //           'Magento\Store\Model\System\Store'
  //       )->getStoreValuesForForm(false, false);
		        /** @var Collection $themesCollections */
        $themesCollections = \Magento\Framework\App\ObjectManager::getInstance()->create('Magento\Theme\Model\Theme\Collection');
		$themesCollections->addConstraint(Collection::CONSTRAINT_AREA, Area::AREA_FRONTEND);
		$themes = [];
		foreach ($themesCollections as $key => $value) {
			$themes[$value->getData('theme_path')] = $value->getData('theme_title');
		}
		$this->getMassactionBlock()->addItem('export', array(
			'label'    => __('Export'),
			'url'      => $this->getUrl('*/*/magicmenu'),
			'additional' => array(
				'visibility' => array(
					'name' => 'theme_path',
					'type' => 'select',
					'class' => 'required-entry',
					'label' => __('Theme'),
					'values' => $themes //$stores
				)
			),
			'confirm'  => __('Are you sure?')
		));
		return $this;
	}

}
