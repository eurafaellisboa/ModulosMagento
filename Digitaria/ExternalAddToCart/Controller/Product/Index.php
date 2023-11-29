<?php
//namespace Digitaria\ExternalAddToCart\Controller\ExternalAddToCart\Product;
namespace Digitaria\ExternalAddToCart\Controller\Product;
 
class Index extends \Magento\Framework\App\Action\Action {

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;
    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $product;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Catalog\Model\Product $product,
        \Magento\Checkout\Model\Cart $cart
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->cart = $cart;
        $this->product = $product;
        parent::__construct($context);
    }
    public function execute()
    {
        try {
            $params = array();
            $params['qty'] = '1';//product quantity
            /* Get product id from a URL like /addtobasket/product?id=1,2,3 */
            $pIds = explode(',',$_GET['id']);
            foreach($pIds as $value) {
                $_product = $this->product->load($value);
                if ($_product) {
                    $this->cart->addProduct($_product, $params);
                    $this->cart->save();
                }
            }

            $this->messageManager->addSuccess(__('Produto adicionado ao carrinho com sucesso'));
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addException(
                $e,
                __('%1', $e->getMessage())
            );
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('error.'));
        }
        /*cart page*/
        $this->getResponse()->setRedirect('/checkout/cart/index');
    }
}