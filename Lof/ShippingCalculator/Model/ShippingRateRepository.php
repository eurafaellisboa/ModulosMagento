<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_ShippingCalculator
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\ShippingCalculator\Model;

use Lof\ShippingCalculator\Api\ShippingRateRepositoryInterface;
use Lof\ShippingCalculator\Api\Data\ShippingRateInterfaceFactory;
use Lof\ShippingCalculator\Api\Data\ShippingRateInterface;
use Lof\ShippingCalculator\Helper\Data;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote\ItemFactory;

/**
 * Class ShippingRateRepository
 * @package Lof\ShippingCalculator\Model
 */
class ShippingRateRepository implements ShippingRateRepositoryInterface
{
    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var ShippingRateInterfaceFactory
     */
    protected $shippingRateInterfaceFactory;

    /**
     * @var ShippingMethodManagement
     */
    protected $shippingMethodManagement;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManagerInterface;

    /**
     * @var ItemFactory
     */
    protected $itemFactory;

    /**
     * ShippingRateRepository constructor.
     *
     * @param Data $helperData
     * @param SerializerInterface $serializer
     * @param DataObjectHelper $dataObjectHelper
     * @param ShippingRateInterfaceFactory $shippingRateInterfaceFactory
     * @param ShippingMethodManagement $shippingMethodManagement
     * @param ProductRepositoryInterface $productRepository
     * @param StoreManagerInterface $storeManagerInterface
     * @param ItemFactory $itemFactory
     */
    public function __construct(
        Data $helperData,
        SerializerInterface $serializer,
        DataObjectHelper $dataObjectHelper,
        ShippingRateInterfaceFactory $shippingRateInterfaceFactory,
        ShippingMethodManagement $shippingMethodManagement,
        ProductRepositoryInterface $productRepository,
        StoreManagerInterface $storeManagerInterface,
        ItemFactory $itemFactory
    ) {
    
        $this->helperData = $helperData;
        $this->serializer = $serializer;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->shippingRateInterfaceFactory = $shippingRateInterfaceFactory;
        $this->shippingMethodManagement = $shippingMethodManagement;
        $this->productRepository = $productRepository;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->itemFactory = $itemFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getRates($request) 
    {
        $productId = $request->getProduct();

        if (!$productId) {
            throw new NoSuchEntityException(__('Shipping Rates requires product id in request.'));
        }

        $cart = $this->helperData->getCart();
        $qty = $request->getQty();
        $qty = $qty?(float)$qty:1;
        $store = $this->storeManagerInterface->getStore();
        $storeId = $store->getId();
        $currencyCode = $store->getCurrentCurrencyCode();

        $product = $this->productRepository->getById($productId, false, $storeId);
        $quote = $cart->getQuote();

        $productParams = $request->getSuperAttribute();//super_attribute
        if ($productParams && !is_array($productParams)) {
            $attributes = explode(",", $productParams);
            if ($attributes && count($attributes)) {
                $newAttributes = [];
                foreach ($attributes as $key => $val) {
                    $tmpAttributes = explode(":", $val);
                    if ($tmpAttributes && count($tmpAttributes) >= 2) {
                        $newAttributes[(int)$tmpAttributes[0]] = (int)$tmpAttributes[1];
                    }
                }
                $productParams = $newAttributes;
            }
        }
        $item = $this->itemFactory->create();
        $item->setProduct($product);
        $item->setQty($qty);
        if ($productParams) {
            $item->addOption($productParams);
        }
        $quote->addItem($item);
        $shipping_data = $request->getShippingData();
        $shippingAddress = $quote->getShippingAddress();
        $shippingAddress->addData($shipping_data->__toArray());
        $carriers = $this->shippingMethodManagement->getShippingMethodsByQuote($quote, $shippingAddress);
        $result = [];
        if ($carriers) {
            $carrierArray = [];
            foreach ($carriers as $carrier) {
                $carrierArray[$carrier->getCarrierCode()] = $carrier->getAmount();
            }
            asort($carrierArray);
            $is_cheapest = [];
            $first_key = array_keys($carrierArray)[0];
            $is_cheapest[$first_key] = $carrierArray[$first_key];

            foreach ($carriers as $carrier) {
                $result[] = $this->getShipingRateResultsDataModel([
                    'carrier_code'  => $carrier->getCarrierCode(),
                    'method_code'   => $carrier->getMethodCode(),
                    'carrier_title' => $carrier->getCarrierTitle(),
                    'method_title'  => $carrier->getMethodTitle(),
                    'amount'        => $carrier->getAmount(),
                    'available'     => (bool)$carrier->getAvailable(),
                    'base_amount'   => $carrier->getBaseAmount(),
                    'error_message' => (string)$carrier->getErrorMessage(),
                    'price_excl_tax'=> $carrier->getPriceExclTax(),
                    'price_incl_tax'=> $carrier->getPriceInclTax(),
                    'is_cheapest' => isset($is_cheapest[$carrier->getCarrierCode()])?1:0
                ]);
            }
        }
        return $result;
    }

    /**
     * Get shipping rate result data model
     * 
     * @param array
     * @return Object
     */
    public function getShipingRateResultsDataModel($arrayData = [])
    {
        $itemDataObject = $this->shippingRateInterfaceFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $itemDataObject,
            $arrayData,
            ShippingRateInterface::class
        );
        
        return $itemDataObject;
    }

}