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


use Magento\Framework\Model\AbstractModel;

class Sharecart extends AbstractModel
{
    /**
     * Define resource model
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\ResourceConnection $rcollection,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        //...
        $this->dateTime=$dateTime;
        $this->rcollection=$rcollection;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

    }
    protected function _construct()
    { 
        $this->_init('FME\ShareCart\Model\ResourceModel\Sharecart');
    }
    public function deleteShareCartByQuoteId($quoteid,$custid)
    {
        $connection = $this->rcollection->getConnection();
        $dateNow=$this->getDateNow();
        $connection = $this->rcollection->getConnection();
        $tableName = $this->rcollection->getTableName('fme_sharecart'); //gives table name with prefix
    //DELETE FROM employees WHERE employeeID = 3
        $sql="Delete FROM ".$tableName." where  quote_id = ".$quoteid." && customer_id=".$custid;//&& consumed =0 && target_user > noof_user && consumed=0 && date(expire_on)>='".$dateNow."' ";
       

        $result = $connection->query($sql); // gives associated array, table fields as key in array.
        return $result;

       // return $collection->getData();;
    }
    public function getLabelByQuoteId($quoteid,$custid)
    {
        $connection = $this->rcollection->getConnection();
        $dateNow=$this->getDateNow();
        $connection = $this->rcollection->getConnection();
        $tableName = $this->rcollection->getTableName('fme_sharecart'); //gives table name with prefix
        $sql="Select * FROM ".$tableName." where  quote_id = ".$quoteid." && customer_id=".$custid;
        $result = $connection->fetchAll($sql); // gives associated array, table fields as key in array.
        return $result;
        
    }
}