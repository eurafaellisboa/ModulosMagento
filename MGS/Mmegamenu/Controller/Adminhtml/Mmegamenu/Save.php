<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MGS\Mmegamenu\Controller\Adminhtml\Mmegamenu;

use Magento\Framework\UrlInterface;

class Save extends \MGS\Mmegamenu\Controller\Adminhtml\Mmegamenu
{
    /**
     * @var Category
     */
    protected $_categoryInstance;

	 /**
     * @var UrlInterface
     */
    private $urlBuilder;
	
    /**
     * Current category key
     *
     * @var string
     */
    protected $_currentCategoryKey;

    /**
     * Array of level position counters
     *
     * @var array
     */
    protected $_itemLevelPositions = [];

    /**
     * Catalog category
     *
     * @var \Magento\Catalog\Helper\Category
     */
    protected $_catalogCategory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * Customer session
     *
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * Catalog layer
     *
     * @var \Magento\Catalog\Model\Layer
     */
    protected $_catalogLayer;

    /**
     * Product collection factory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\Indexer\Category\Flat\State
     */
    protected $flatState;
	
	/**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
	
	/**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;
	
	/**
     * @var \MGS\Mmegamenu\Model\CacheFactory
     */
    protected $_modelMenuCache;
	
	/**
	 * @var \Magento\Catalog\Model\CategoryRepository
	 */
	protected $categoryRepository;
	

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Catalog\Model\Category $modelCategory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Catalog\Helper\Category $catalogCategory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\Indexer\Category\Flat\State $flatState
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Catalog\Helper\Category $catalogCategory,
        \Magento\Framework\Registry $registry,
		\Magento\Store\Model\StoreManagerInterface $_storeManager,
        \Magento\Catalog\Model\Indexer\Category\Flat\State $flatState,
        \MGS\Mmegamenu\Model\CacheFactory $modelMenuCache,
		\Magento\Framework\ObjectManagerInterface $objectManager,
		\Magento\Catalog\Model\CategoryRepository $categoryRepository,
		UrlInterface $urlBuilder,
		\Magento\Cms\Model\Template\FilterProvider $filterProvider
    ) {
        $this->_productCollectionFactory = $productCollectionFactory;
		$this->categoryRepository = $categoryRepository;
        $this->_catalogLayer = $layerResolver->get();
        $this->httpContext = $httpContext;
        $this->_catalogCategory = $catalogCategory;
		$this->_storeManager = $_storeManager;
        $this->_registry = $registry;
        $this->flatState = $flatState;
        $this->_modelMenuCache = $modelMenuCache;
        $this->_categoryInstance = $categoryFactory->create();
		$this->_objectManager = $objectManager;
		$this->_filterProvider = $filterProvider;
		$this->_urlBuilder = $urlBuilder;
        parent::__construct($context);
    }
    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if data sent
        $data = $this->getRequest()->getPostValue();
		
        if ($data) {
            $id = $this->getRequest()->getParam('megamenu_id');
            $model = $this->_objectManager->create('MGS\Mmegamenu\Model\Mmegamenu')->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addError(__('This item no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }
			
			if(isset($data['sub_category'])){
				$data['sub_category_ids'] = implode(',', $data['sub_category']);
			}
			
			if($data['menu_type'] == 2){ 
				$data['category_id'] = 0; 
				$data['sub_category'] = $data['top_content'] = $data['bottom_content'] = $data['sub_category_ids'] = '';
			}
			else{
				$data['static_content'] = '';
			}
			
			if(!isset($data['stores'])){
				$data['stores'] = NULL;
			}
			
			if(!isset($data['sub_category_ids'])){
				$data['sub_category_ids'] = NULL;
			}
			
			$item = array(
				'id' => rand() . time(),
				'parent_id' => $data['parent_id'],
				'menu_type' => $data['menu_type'],
				'url' => $data['url'],
				'title' => $data['title'],
				'category_id' => $data['category_id'],
				'sub_category_ids' => $data['sub_category_ids'],
				'use_thumbnail' => $data['use_thumbnail'],
				'position' => $data['position'],
				'columns' => $data['columns'],
				'special_class' => $data['special_class'],
				'static_content' => $data['static_content'],
				'top_content' => $data['top_content'],
				'bottom_content' => $data['bottom_content'],
				'left_content' => $data['left_content'],
				'left_col' => $data['left_col'],
				'right_col' => $data['right_col'],
				'right_content' => $data['right_content'],
				'status' => $data['status'],
				'html_label' => $data['html_label'],
			);

            // init model and set data

            $model->setData($data);

            // try to save it
            try {
                // save the data
                $model->save();
				
				/* Remove Cache */
				$modelCache = $this->_modelMenuCache->create()->getCollection()->addFieldToFilter('parent_id', $model->getId());
				foreach ($modelCache as $_modelCache) {
					$modelCacheItem = $this->_objectManager->create('MGS\Mmegamenu\Model\Cache')
						->load($_modelCache->getCacheId())
						->delete();
                }
				/* Get Store */
				$allStores = $this->_storeManager->getStores();
				$_storeData = array();
				foreach ($allStores as $key => $_store) {
					$_storeData['parent_id'] = $model->getId();
					$_storeData['store_id'] = $_store->getId();
					$_storeData['parent_menu_id'] = $data['parent_id'];
					$_storeData['html'] = $this->getMenuHtml($item, $_store->getId());
					$cacheItem = $this->_objectManager->create('MGS\Mmegamenu\Model\Cache');
					$cacheItem->setData($_storeData);
					$cacheItem->save();
				}
				
                // display success message
                $this->messageManager->addSuccess(__('You saved the item.'));
                // clear previously saved data from session
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);

                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
                }
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                // save data in session
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData($data);
                // redirect to edit form
                return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
            }
        }
        return $resultRedirect->setPath('*/*/');
    }
	
	/**
     * Get url for category data
     *
     * @param Category $category
     * @return string
     */
    public function getCategoryUrl($category)
    {
        if ($category instanceof Category) {
            $url = $category->getUrl();
        } else {
            $url = $this->_categoryInstance->setData($category->getData())->getUrl();
        }

        return $url;
    }
	
	public function getMenuHtml($item, $storeId) {
        if ($item['menu_type'] == 2) {
            return $this->getStaticMenu($item);
        } else {
            return $this->getCategoryMenu($item, $storeId);
        }
    }
	
	public function getStaticMenu($item) {

		if (filter_var($item['url'], FILTER_VALIDATE_URL)) { 
			$itemUrl = $item['url'];
		}else{
			$itemUrl = $itemUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB) .$item['url'];
		}
		
		$html = '<a href="' . $itemUrl . '" class="level0">';

        if ($item['html_label'] != '') {
            $html.=$item['html_label'];
        }
		
        $html.='<span>'.$item['title'].'</span></a>';
		
        if ($item['static_content'] != '') {
            $html.='<span class="toggle-menu"><span class="icon-toggle"></span></span>';
			
            $html.='<ul class="dropdown-mega-menu"><li>' . $item['static_content'] . '</li></ul>';
        }
		
        return $html;
    }
	
	public function getCategoryMenu($item, $storeId) {
        $html = '<a';
		
        $categoryId = $item['category_id'];
		$itemUrl = '#';
		
        if ($categoryId) {
			$category = $this->categoryRepository->get($categoryId, $storeId);
            $html.=' href="';
            if ($item['url'] != '') {
				if (filter_var($item['url'], FILTER_VALIDATE_URL)) { 
					$itemUrl = $item['url'];
				}else{
					$itemUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB) .$item['url'];
				}
            } else {
                if ($this->_storeManager->getStore()->getRootCategoryId() == $category->getId()) {
					$itemUrl = '#';
                } else {
					$itemUrl = $this->getCategoryUrl($category);
                }
            }
        }
		
		$html .= $itemUrl . '" class="level0">';
		
        if ($item['html_label'] != '') {
            $html.=$item['html_label'];
        }
        $html.='<span>'.$item['title'].'</span></a>';

        $subCatAccepp = $this->getSubCategoryAccepp($categoryId, $item, $storeId);
        if (count($subCatAccepp) > 0 || $item['top_content'] != '' || $item['bottom_content'] != '') {
			
            $html.='<span class="toggle-menu"><span class="icon-toggle"></span></span>';
			
            $html.='<ul class="dropdown-mega-menu"><li>';
			
            $columnAccepp = count($subCatAccepp);
			$arrColumn = [];
            if ($columnAccepp > 0) {
                $columns = $item['columns'];
				if($columns > 1 && $item['left_content']!='' && $item['left_col']!=0){
					$columns = $columns - $item['left_col'];
				}
				
				if($columns > 1 && $item['right_content']!='' && $item['right_col']!=0){
					$columns = $columns - $item['right_col'];
				}

                $arrOneElement = array_chunk($subCatAccepp, 1);
                $countCat = count($subCatAccepp);
                $count = 0;
                while ($countCat > 0) {
                    for ($i = 0; $i < $columns; $i++) {
                        if (isset($subCatAccepp[$count])) {
                            $arrColumn[$i][] = $subCatAccepp[$count];
                            $count++;
                        }
                    }
                    $countCat--;
                }

                $newArrColumn = [];
                $newCount = 0;
				
				for ($i = 0; $i < count($arrColumn); $i++) {
					$newColumn = count($arrColumn[$i]);
					for ($j = 0; $j < $newColumn; $j++) {
						$newArrColumn[$i][$j] = $subCatAccepp[$newCount];
						$newCount++;
					}
				}

                $arrColumn = $newArrColumn;

                

                if ($columns > 1) {
                    $html.= '<div class="mega-menu-content"><div class="line">';

                    if ($item['top_content'] != '') {
                        $html.='<div class="top_content static-content col-des-12">' . $item['top_content'] . '</div>';
                    }
					
					if($item['left_content']!='' && $item['left_col']!=0){
						$html.='<div class="left_content static-content col-des-'.$this->getColumnByCol($item['columns']) * $item['left_col'].'">' . $item['left_content'] . '</div>';
					}
                } else {
                    $html.= '<ul class="dropdown-submenu-ct">';
                }
                foreach ($arrColumn as $_arrColumn) {
                    $html.= $this->drawListSub($item, $_arrColumn, $storeId);
                }

                if ($columns > 1) {
					if($item['right_content']!='' && $item['right_col']!=0){
						$html.='<div class="right_content static-content col-des-'.$this->getColumnByCol($item['columns']) * $item['right_col'].'">' . $item['right_content'] . '</div>';
					}

                    if ($item['bottom_content'] != '') {
                        $html.='<div class="bottom_content static-content col-des-12">' . $item['bottom_content'] . '</div>';
                    }

                    $html.= '</div></div>';
                } else {
                    $html.= '</ul>';
                }
            }


            $html.='</li></ul>';
        }

        return $html;
    }
	
	public function getColumnByCol($col) {
        return 12/$col;
    }
	
	public function getSubCategoryAccepp($categoryId, $item, $storeId) {
        $subCatExist = explode(',', $item['sub_category_ids']);

		$category = $this->categoryRepository->get($categoryId, $storeId);

        $children = $category->getChildrenCategories();
        $childrenCount = count($children);

        $subCatId = array();
        if ($childrenCount > 0) {
            foreach ($children as $child) {
                if (in_array($child->getId(), $subCatExist)) {
                    $subCatId[] = $child->getId();
                }
            }
        }
        return $subCatId;
    }
	
	public function drawListSub($item, $catIds, $storeId) {
        $html = '';

        if ($item['columns'] > 1) {
            $html.='<div class="col-des-' . $this->getColumnByCol($item['columns']) . '"><ul class="sub-menu">';
        }

        if (count($catIds) > 0) {
            foreach ($catIds as $categoryId) {
				$category = $this->categoryRepository->get($categoryId, $storeId);
                $html.= $this->drawList($category, $item, 1, $storeId);
            }
        }

        if ($item['columns'] > 1) {
            $html.='</ul></div>';
        }

        return $html;
    }
	
	public function drawList($category, $item, $level, $storeId) {
		$maxLevel = 10;
		$mediaUrl = $this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]);

        $children = $this->getSubCategoryAccepp($category->getId(), $item, $storeId);
        $childrenCount = count($children);

        $htmlLi = '<li class="level'.$level;
        
        if ($childrenCount > 0 && $item['columns'] == 1) {
            $htmlLi .= ' dropdown-submenu';
        }

        $htmlLi .= '">';
		
        $html[] = $htmlLi;
		
		if ($category->getFbuilderThumbnail() !="" && $item['columns'] > 1 && $level == 1 && $item['use_thumbnail'] == 1) {
			$imageCate = $category->getFbuilderThumbnail();
			$imageCateUrl =  $mediaUrl . 'catalog/category/' . $imageCate;
			
			$html[] = '<a class="category-image" href="' . $this->getCategoryUrl($category) . '"><img alt="'.$category->getName().'" class="img-fluid img-full" src="'.$imageCateUrl.'" /></a>';
			
        }
		
        $html[] = '<a href="' . $this->getCategoryUrl($category) . '">';
        if ($item['columns'] > 1 && $level == 1) {
            $html[] = '<span class="mega-menu-sub-title">';
        }

        $html[] = $category->getName();

		if($category->getMgsMegamenuItemLabel()){
			$backgroundLabel = "";
			if($category->getMgsMegamenuItemBackground()){
				$backgroundLabel = $category->getMgsMegamenuItemBackground();
			}
			if($backgroundLabel != ""){
				$html[] = '<span class="label-menu" style="background-color: '.$backgroundLabel.'; border-color: '.$backgroundLabel.';">';
			}else {
				$html[] = '<span class="label-menu">';
			}
			$html[] = $category->getMgsMegamenuItemLabel();
			$html[] = '</span>';
		}
		
        if ($item['columns'] > 1 && $level == 1) {
            $html[] = '</span>';
        }
		
		if ($childrenCount > 0 && $item['columns'] == 1) {
            $html[] = '<span class="toggle-menu"><span class="icon-toggle"></span></span>';
        }
		
        $html[] = '</a>';

        if ($level < $maxLevel) {
            $maxSub = 50;
			
            $htmlChildren = '';
            if ($childrenCount > 0) {
                $i = 0;
                foreach ($children as $child) {
                    $i++;
                    if ($i <= $maxSub) {
						$_child = $this->categoryRepository->get($child, $storeId);
                        $htmlChildren .= $this->drawList($_child, $item, ($level + 1), $storeId);
                    }
                }
            }
            if (!empty($htmlChildren)) {
                $html[] = '<span class="toggle-menu"><span class="icon-toggle"></span></span>';

                $html[] = '<ul';
                if ($item['columns'] > 1) {
                    $html[] = ' class="sub-menu"';
                } else {
                    $html[] = ' class="dropdown-menu-ct"';
                }
                $html[] = '>';
                $html[] = $htmlChildren;
                $html[] = '</ul>';
            }
        }
        $html[] = '</li>';
        $html = implode("\n", $html);
        return $html;
    }
}
