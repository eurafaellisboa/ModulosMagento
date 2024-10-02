<?php
namespace Digitaria\RDMarketing\Model;

use Magento\Sales\Model\Order;
use Magento\Checkout\Model\Session;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class OrderData
{
    protected $checkoutSession;
    protected $order;
    protected $priceHelper;
    protected $timezone;

    public function __construct(
        Session $checkoutSession,
        Order $order,
        PriceHelper $priceHelper,
        TimezoneInterface $timezone
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->order = $order;
        $this->priceHelper = $priceHelper;
        $this->timezone = $timezone;
    }

    public function getOrderData($orderId)
    {
        //$orderId = $this->checkoutSession->getLastRealOrderId();
        $order = $this->order->load($orderId);
		 
		 
		 if ($order === null) {
    		throw new \Exception("Order object is null.");
		 }

		 
		 $customerBirthday = $order->getCustomerDob();
    	 $customerBirthdayFormatted = $this->getFormattedDate($customerBirthday);
		 
        $data = [
            'order_id' => $order->getIncrementId(),
            'customer_name' => $order->getCustomerName(),
            'customer_phone' => $order->getBillingAddress()->getTelephone(),
            'customer_city' => $order->getBillingAddress()->getCity(),
            'customer_email' => $order->getCustomerEmail(),
            'order_date' => $this->getFormattedDate($order->getCreatedAt()),
            'order_total' => $this->formatPrice($order->getGrandTotal(), 'FLOAT'),
			'order_items_count' => (int)$order->getTotalItemCount(),
            'order_items' => $this->getOrderItems($order),
            'shipping_method' => $order->getShippingDescription(),
            'payment_method' => $order->getPayment()->getMethodInstance()->getTitle(),
            'billing_address' => $order->getBillingAddress(),
            'coupon_code' => $order->getCouponCode(),
            'customer_birthday' => $customerBirthdayFormatted,
            'order_status' => $order->getStatusLabel(),
        ];

        return $data;
    }
	

    protected function getFormattedDate($date)
    {
        //return $this->timezone->date(new \DateTime($date))->format('d/m/Y');
		  return $this->timezone->date(new \DateTime($date))->format('Y-m-d');
    }

    protected function formatPrice($price, $dataType)
    {
        if ($dataType === 'FLOAT') {
            return (float) $price;
        } elseif ($dataType === 'INTEGER') {
            return (int) $price;
        } else {
            return $price;
        }
    }

    protected function getOrderItems($order)
    {
        $items = [];
        foreach ($order->getAllItems() as $item) {
            $items[] = [
                //'name' => $item->getName(),
                'sku' => $item->getSku(),
                //'price' => $this->formatPrice($item->getPrice(), 'FLOAT'),
                //'qty' => $this->formatPrice($item->getQtyOrdered(), 'INTEGER'),
                //'subtotal' => $this->formatPrice($item->getRowTotal(), 'FLOAT'),
            ];
        }
        return $items;
    }
}
