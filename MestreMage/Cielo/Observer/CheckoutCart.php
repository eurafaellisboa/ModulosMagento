<?php
namespace MestreMage\Cielo\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Event\Observer;
class CheckoutCart implements ObserverInterface
{
    /**
     * @var ManagerInterface
     */
    protected $messageManager;
    /**
     * @var RedirectInterface
     */
    protected $redirect;
    /**
     * @var Cart
     */
    protected $cart;
    /**
     * @param ManagerInterface $messageManager
     * @param RedirectInterface $redirect
     * @param CustomerCart $cart
     */
    public function __construct(
        ManagerInterface $messageManager,
        RedirectInterface $redirect,
        CustomerCart $cart
    ) {
        $this->messageManager = $messageManager;
        $this->redirect = $redirect;
        $this->cart = $cart;
    }
    /**
     * Validate Cart Before going to checkout
     * - event: controller_action_predispatch_checkout_index_index
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $controller = $observer->getControllerAction();
        $itemsVisible = $this->cart->getQuote()->getAllItems();

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $scopeConfig = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
        $inc_current = 0;
        $inc_not_current = 0;
        if($scopeConfig->getValue("payment/mestremagecc/recurrent", $storeScope)) {
            foreach ($itemsVisible as $item) {
                if ($item->getData('product_type') == 'simple') {
                    $_product = $objectManager->get('Magento\Catalog\Model\Product')->load($item->getProductId());
                    if ($_product->getAttributeText('recurrentpayment')) {
                        $inc_current++;
                    } else {
                        $inc_not_current++;
                    }
                } else {
                    $inc_not_current++;
                }
            }

             if (($inc_current && $inc_not_current) || $inc_current > 1) {
                $this->messageManager->addNoticeMessage(
                    __($scopeConfig->getValue("payment/mestremagecc/recurrent_msg1", $storeScope))
                );
                $this->redirect->redirect($controller->getResponse(), 'checkout/cart');
            }
        }
    }
}