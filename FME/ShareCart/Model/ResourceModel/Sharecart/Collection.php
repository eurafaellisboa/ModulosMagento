<?php
/**
 * FME Extensions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the fmeextensions.com license that is
 * available through the world-wide-web at this URL:
 * https://www.fmeextensions.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  FME
 * @author     Atta <support@fmeextensions.com>
 * @package   FME_ShareCart
 * @copyright Copyright (c) 2019 FME (http://fmeextensions.com/)
 * @license   https://fmeextensions.com/LICENSE.txt
 */
namespace FME\ShareCart\Model\ResourceModel\Sharecart;

use \FME\ShareCart\Model\ResourceModel\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'sharecart_id';

    protected $_previewFlag;

    protected function _construct()
    {
       
        $this->_init(
            'FME\ShareCart\Model\Sharecart',
            'FME\ShareCart\Model\ResourceModel\Sharecart'
        );
        //$this->_map['fields']['testimonial_id'] = 'main_table.testimonial_id';
        //$this->_map['fields']['store'] = 'store_table.store_id';
    }

    public function setFirstStoreFlag($flag = false)
    {
        $this->_previewFlag = $flag;
        return $this;
    }
    public function addStoreFilter($store, $withAdmin = true)
    {
        if (!$this->getFlag('store_filter_added')) {
            $this->performAddStoreFilter($store, $withAdmin);
        }
        return $this;
    }

    protected function _afterLoad()
    {
        //print_r($this->performAfterLoad('fme_testimonials_store', 'testimonial_id'));exit;

        // $this->performAfterLoad('fme_testimonials_store', 'testimonial_id');
        // $this->performAfterForLikes('fme_testimonials_votes', 'testimonial_id');
        // $this->performAfterForDisLikes('fme_testimonials_votes', 'testimonial_id');
        // //performAfterForDisLikes
        // $this->_previewFlag = false;
        return parent::_afterLoad();
    }

    protected function _renderFiltersBefore()
    {
        //$this->joinStoreRelationTable('fme_testimonials_store', 'testimonial_id');
    }
}
