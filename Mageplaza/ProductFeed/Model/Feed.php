<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_ProductFeed
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ProductFeed\Model;

use Magento\Backend\Model\Session;
use Magento\Catalog\Model\ProductFactory;
use Magento\CatalogRule\Model\Rule\Condition\Combine;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Model\ResourceModel\Iterator;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Rule\Model\AbstractModel;
use Mageplaza\ProductFeed\Helper\Data;

/**
 * Class Feed
 * @package Mageplaza\ProductFeed\Model
 */
class Feed extends AbstractModel
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'mageplaza_productfeed_feed';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'mageplaza_productfeed_feed';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'mageplaza_productfeed_feed';

    /**
     * @var Iterator
     */
    protected $resourceIterator;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var array
     */
    protected $productCollection = [];

    /**
     * @var
     */
    protected $productIds;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var Session
     */
    protected $backendSession;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * Feed constructor.
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param TimezoneInterface $localeDate
     * @param Iterator $resourceIterator
     * @param ProductFactory $productFactory
     * @param RequestInterface $request
     * @param Session $backendSession
     * @param Data $helperData
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        TimezoneInterface $localeDate,
        Iterator $resourceIterator,
        ProductFactory $productFactory,
        RequestInterface $request,
        Session $backendSession,
        Data $helperData,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        parent::__construct($context, $registry, $formFactory, $localeDate, $resource, $resourceCollection, $data);

        $this->resourceIterator = $resourceIterator;
        $this->productFactory = $productFactory;
        $this->request = $request;
        $this->backendSession = $backendSession;
        $this->helperData = $helperData;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mageplaza\ProductFeed\Model\ResourceModel\Feed');
    }

    /**
     * @return \Magento\CatalogRule\Model\Rule\Condition\Combine|\Magento\Rule\Model\Action\Collection|\Magento\Rule\Model\Condition\Combine
     */
    public function getConditionsInstance()
    {
        return $this->getActionsInstance();
    }

    /**
     * @return \Magento\CatalogRule\Model\Rule\Condition\Combine|\Magento\Rule\Model\Action\Collection
     */
    public function getActionsInstance()
    {
        return ObjectManager::getInstance()->create(Combine::class);
    }

    /**
     * @param bool $isGenerate
     * @return array|null
     * @throws \Zend_Serializer_Exception
     */
    public function getMatchingProductIds($isGenerate = false)
    {
        if ($this->productIds === null) {
            if($isGenerate){
                $data = $this->request->getPost('rule');
                $storeId = isset($this->request->getPost('feed')['store_id']) ? $this->request->getPost('feed')['store_id'] : null;

                if ($data) {
                    $this->backendSession->setProductFeedData(['rule' => $data, 'store_id' => $storeId]);
                } else {
                    $productFeedData = $this->backendSession->getProductFeedData();
                    //$data = $productFeedData['rule'];
					 $data = isset($productFeedData['rule']) ? count($productFeedData['rule']) : 0;
					 $storeId  = isset($productFeedData['store_id']) ? count($productFeedData['store_id']) : 0;
                    //$storeId = $productFeedData['store_id'];
                }
                if (!$data) {
                    $data = [];
                }
                $this->loadPost($data);
            }else{
                $storeId = $this->getStoreId();
            }

            $this->productIds = [];
            $this->setCollectedAttributes([]);

            /** @var $productCollection \Magento\Catalog\Model\ResourceModel\Product\Collection */
            $productCollection = $this->productFactory->create()->getCollection();
            $productCollection->addAttributeToSelect('*')
                ->addAttributeToFilter('status', 1)->addStoreFilter($storeId);

            $this->setConditionsSerialized($this->helperData->serialize($this->getConditions()->asArray()));
            $this->getConditions()->collectValidatedAttributes($productCollection);
            $this->resourceIterator->walk(
                $productCollection->getSelect(),
                [[$this, 'callbackValidateProductConditions']],
                [
                    'attributes' => $this->getCollectedAttributes(),
                    'product'    => $this->productFactory->create()
                ]
            );
        }

        return $this->productIds;
    }

    /**
     * Callback function for product matching (conditions)
     *
     * @param array $args
     * @return void
     */
    public function callbackValidateProductConditions($args)
    {
        $product = clone $args['product'];
        $product->setData($args['row']);

        if ($this->getConditions()->validateByEntityId($product->getId())) {
            $this->productIds[] = $product->getId();
        }
    }
}
