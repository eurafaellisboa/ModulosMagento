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
namespace FME\ShareCart\Model;

class Sharecart extends \Magento\Framework\Model\AbstractModel
{
    protected $storeManager;
    protected $_objectManager;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\ResourceConnection $coreResource,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) { 
            
        $this->storeManager = $storeManager;
        $this->_objectManager = $objectManager;
        $this->_coreResource = $coreResource;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }
        
    protected function _construct()
    {
        $this->_init('FME\ShareCart\Model\ResourceModel\Sharecart');
    }
        
    public function getAvailableStatuses()
    {
        $availableOptions = ['1' => 'Enable',
                          '0' => 'Disable'];
        return $availableOptions;
    }
        
    public function getTestimonialsList()
    {
            
        $collection = $this->getCollection()->addFieldToFilter('status', 1);
        $testimonialList = [];
            
        foreach ($collection as $data) {
            $testimonialList[$data->getId()] = $data->getTitle();
        }
            
        return $testimonialList;
    }
    public function gethelpfullData($isimage,$ord)
    {
        $mainTable = $this->_coreResource
            ->getTableName('fme_testimonials');
        $votestable = $this->_coreResource
            ->getTableName('fme_testimonials_votes');
        //echo $photogalleryTable;exit;
        if($isimage=='1')
        {
            $select = "SELECT ".$mainTable.".*,count(".$votestable.".islike) as likes from ".$mainTable." LEFT JOIN ".$votestable." on ((".$mainTable.".testimonial_id = ".$votestable.".testimonial_id) AND ".$votestable.".islike='1') Where ".$mainTable.".image != '' AND ".$mainTable.".status='1' GROUP by ".$mainTable.".testimonial_id ORDER BY likes ".$ord;
       
        }
        else{

            $select = "SELECT ".$mainTable.".*,count(".$votestable.".islike) as likes from ".$mainTable." LEFT JOIN ".$votestable." on ((".$mainTable.".testimonial_id = ".$votestable.".testimonial_id) AND ".$votestable.".islike='1')Where  ".$mainTable.".status='1' GROUP by ".$mainTable.".testimonial_id ORDER BY likes ".$ord;
       
        }

    // echo $select;//exit;
        //$connection = $this->_objectManager->create('\Magento\Framework\App\ResourceConnection');
        $conn = $this->_coreResource->getConnection();
      //  print_r()
     
        $data = $conn->fetchAll($select);
       /// print_r($data);exit;
        return $data;
        //print_r($data);
    }
    public function getTestimonials()
    {
        $testimonial = [];
        $collection = $this->getCollection()->addFieldToFilter('status', 1);
        $testimonialList = [];
        $i=0;
        foreach ($collection as $data) {
            $testimonial[$i] =['value' => $data->gettestimonial_id(), 'label' => __($data->getTitle())];
            $i++;
        }
        return $testimonial;
    }
}
