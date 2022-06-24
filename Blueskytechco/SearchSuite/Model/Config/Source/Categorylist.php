<?php
namespace Blueskytechco\SearchSuite\Model\Config\Source;

class Categorylist implements \Magento\Framework\Option\ArrayInterface
{
    protected $_categoryHelper;
    protected $_request;
    protected $storeManager;
    protected $categoryRepository;

    public function __construct
    (
        \Magento\Catalog\Helper\Category $catalogCategory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->_categoryHelper = $catalogCategory;
        $this->_request = $request;
        $this->storeManager = $storeManager;
        $this->categoryRepository = $categoryRepository;
    }

    /*  
     * Option getter
     * @return array
     */
    public function toOptionArray()
    {

        $arr = $this->toArray();
        $ret = [];

        foreach ($arr as $key => $value)
        {

            $ret[] = [
                'value' => $key,
                'label' => $value
            ];
        }

        return $ret;
    }

    /*
     * Get options in "key-value" format
     * @return array
     */
    public function toArray()
    {
        $store = $this->_request->getParam('store');
        $storeGroupId = $this->storeManager->getStore($store)->getStoreGroupId();
        $rootCategoryId = $this->storeManager->getGroup($storeGroupId)->getRootCategoryId();
        $categoryRoot = $this->categoryRepository->get($rootCategoryId, $store);
        $categories = $categoryRoot->getChildrenCategories();
        $catagoryList = array();
        foreach ($categories as $category){
            $catagoryList[$category->getEntityId()] = __($category->getName());
        }
        return $catagoryList;
    }
}

