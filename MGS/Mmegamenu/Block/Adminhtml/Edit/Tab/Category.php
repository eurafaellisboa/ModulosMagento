<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Related products admin grid
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace MGS\Mmegamenu\Block\Adminhtml\Edit\Tab;

class Category extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
	
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;
	
	protected $_ids;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
		\Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
		$this->_objectManager = $objectManager;
		$this->_storeManager = $context->getStoreManager();
        parent::__construct($context, $registry, $formFactory, $data);
    }
	
	public function getStore(){
		return $this->_storeManager->getStore();
	}
	
	public function getModel($model){
		return $this->_objectManager->create($model);
	}
	
	/**
     * Init form
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('MGS_Mmegamenu::category.phtml');
    }
	
	/**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Category');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Category');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
	
	public function getRootCategoryId(){
		$store = $this->getStore();
		if($this->getRequest()->getParam('store')!='' && $this->getRequest()->getParam('store')!=0){
			$storeModel = $this->getModel('Magento\Store\Model\Store');
			$store = $storeModel->load($this->getRequest()->getParam('store'));
		}
		$categoryId = $store->getRootCategoryId();
		return $categoryId;
	}
	
	public function getCategory(){
		$categoryId = $this->getRootCategoryId();
		$category = $this->getModel('Magento\Catalog\Model\Category')->load($categoryId);
		return $category;
	}
	
	public function getIds(){
		return $this->_ids;
	}
	
	public function getTreeCategory($category, $parent, $ids = array()){
		if ($this->getRequest()->getParam('id')) {
			$megamenu = $this->getModel('MGS\Mmegamenu\Model\Mmegamenu')->load($this->getRequest()->getParam('id'));
			if($megamenu->getCategoryId()!=0){
				$categoryId = $megamenu->getCategoryId();
			}
			else{
				$categoryId = 0;
			}
        }
		else{
			$categoryId = 0;
		}
		
		$children = $category->getChildrenCategories();
		$childrenCount = count($children);
		
		$htmlLi = '<li>';
		$html[] = $htmlLi;
		//if($this->isCategoryActive($category)){
		$ids[] = $category->getId();
		$this->_ids = implode(",", $ids);
		//}
		
		$html[] = '<a id="node'.$category->getId().'">';

		$html[] = '<input lang="'.$category->getId().'" onclick="setCheckbox('.$category->getId().')" type="radio" id="radio'.$category->getId().'" name="category_id" value="'.$category->getId().'" class="radio checkbox'.$parent.'"';
		if($categoryId == $category->getId()){
			$html[] = ' checked="checked"';
		}
		$html[] = '/><label for="radio'.$category->getId().'">' . $category->getName() . '</label>';

		$html[] = '</a>';
		
		$htmlChildren = '';
		if($childrenCount>0){
			foreach ($children as $child) {

				$_child = $this->getModel('Magento\Catalog\Model\Category')->load($child->getId());
				$htmlChildren .= $this->getTreeCategory($_child, $category->getId(), $ids);
			}
		}
		if (!empty($htmlChildren)) {
            $html[] = '<ul id="container'.$category->getId().'">';
            $html[] = $htmlChildren;
            $html[] = '</ul>';
        }

        $html[] = '</li>';
        $html = implode("\n", $html);
        return $html;
	}

}
