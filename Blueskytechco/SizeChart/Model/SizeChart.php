<?php
namespace Blueskytechco\SizeChart\Model;

use Magento\Framework\Model\AbstractModel;
use \Blueskytechco\SizeChart\Api\Data\DataInterface;
use \Magento\Framework\DataObject\IdentityInterface;
use Blueskytechco\SizeChart\Model\ResourceModel\SizeChart as SizeChartResourceModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;

class SizeChart extends AbstractModel implements DataInterface, IdentityInterface
{
	const CACHE_TAG = 'size_chart';
    /**
     * Pattern constructor.
     * @param Context $context
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        Registry $registry
    ) {
		$this->_cacheTag = 'size_chart';
        $this->_eventPrefix = 'size_chart';
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
        $this->_init(SizeChartResourceModel::class);
    }
	
	/**
     * {@inheritdoc}
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
	
	/**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }
	
	/**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->setData(self::NAME, $name);
    }
}
