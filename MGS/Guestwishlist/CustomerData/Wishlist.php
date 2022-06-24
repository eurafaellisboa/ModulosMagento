<?php
namespace MGS\Guestwishlist\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;

/**
 * Wishlist section
 */
class Wishlist implements SectionSourceInterface
{
    /**
     * @var string
     */
    const SIDEBAR_ITEMS_NUMBER = 6;

    /**
     * @var \MGS\Guestwishlist\Helper\Data
     */
    protected $wishlistHelper;
	
    /**
     * @var \Magento\Wishlist\Helper\Data
     */
    protected $wishlistLogedHelper;

    /**
     * @var \Magento\Catalog\Helper\ImageFactory
     */
    protected $imageHelperFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @param \MGS\Guestwishlist\Helper\Data $wishlistHelper
     * @param \Magento\Wishlist\Helper\Data $wishlistLogedHelper
     * @param \Magento\Catalog\Helper\ImageFactory $imageHelperFactory
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \MGS\Guestwishlist\Helper\Data $wishlistHelper,
        \Magento\Wishlist\Helper\Data $wishlistLogedHelper,
        \Magento\Catalog\Helper\ImageFactory $imageHelperFactory,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->wishlistHelper = $wishlistHelper;
        $this->imageHelperFactory = $imageHelperFactory;
        $this->wishlistLogedHelper = $wishlistLogedHelper;
        $this->customerSession = $customerSession;
    }

    /**
     * {@inheritdoc}
     */
    public function getSectionData()
    {
        $counter = $this->getCounter();
        return [
            'counter' => $counter,
            'items' => $counter ? $this->getItems() : [],
        ];
    }

    /**
     * @return string
     */
    protected function getCounter()
    {
        return $this->createCounter($this->wishlistHelper->getItemCount());
    }

    /**
     * Create button label based on wishlist item quantity
     *
     * @param int $count
     * @return \Magento\Framework\Phrase|null
     */
    protected function createCounter($count)
    {
        /*
         * we don't need guest wishlist when user logged in
         */
        if ($this->customerSession->isLoggedIn()) {
			if($this->wishlistLogedHelper->getItemCount() >= 1){
				return $this->wishlistLogedHelper->getItemCount();
			}
            return null;
        }
        if ($count >= 1) {
            return $count;
        }
        return null;
    }

    /**
     * Get wishlist items
     *
     * @return array
     */
    protected function getItems()
    {
        $collection = $this->wishlistHelper->getWishlistItemCollection(self::SIDEBAR_ITEMS_NUMBER);
        $items = [];
        foreach ($collection as $wishlistItem) {
            $items[] = $this->getItemData($wishlistItem);
        }
        return $items;
    }

    /**
     * Retrieve wishlist item data
     *
     * @param array $wishlistItem
     * @return array
     */
    protected function getItemData($wishlistItem)
    {
        $product = $wishlistItem['product'];
        return [
            'image' => $this->getImageData($product),
            'product_url' => $product->getUrlModel()->getUrl($product, []),
            'product_name' => $product->getName(),
            'product_price' => $this->wishlistHelper->formatPrice($product->getFinalPrice()),
            'product_is_saleable_and_visible' => $product->isSaleable() && $product->isVisibleInSiteVisibility(),
            'product_has_required_options' => $product->getTypeInstance()->hasRequiredOptions($product),
            'add_to_cart_params' => $this->wishlistHelper->getItemCartParams($wishlistItem),
            'delete_item_params' => $this->wishlistHelper->getDeleteItemParams($wishlistItem, $product, $product->getName())
        ];
    }

    /**
     * Retrieve product image data
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\Catalog\Block\Product\Image
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function getImageData($product)
    {
        /** @var \Magento\Catalog\Helper\Image $helper */
        $helper = $this->imageHelperFactory->create()
            ->init($product, 'wishlist_sidebar_block');

        $template = $helper->getFrame()
            ? 'Magento_Catalog/product/image'
            : 'Magento_Catalog/product/image_with_borders';

        $imagesize = $helper->getResizedImageInfo();

        $width = $helper->getFrame()
            ? $helper->getWidth()
            : (!empty($imagesize[0]) ? $imagesize[0] : $helper->getWidth());

        $height = $helper->getFrame()
            ? $helper->getHeight()
            : (!empty($imagesize[1]) ? $imagesize[1] : $helper->getHeight());

        return [
            'template' => $template,
            'src' => $helper->getUrl(),
            'width' => $width,
            'height' => $height,
            'alt' => $helper->getLabel(),
        ];
    }
}
