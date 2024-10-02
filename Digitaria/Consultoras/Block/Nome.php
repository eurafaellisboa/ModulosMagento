<?php
namespace Digitaria\Consultoras\Block;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Element\Template;

class Nome extends \Magento\Framework\View\Element\Template
{

    protected $_categoryFactory;
    protected $_category;
    protected $_categoryHelper;
    protected $_categoryRepository;
	protected $registry;
	protected $_catalogSession;
	protected $customerSession;
	
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,        
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Helper\Category $categoryHelper,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,   
		\Magento\Framework\Registry $registry,
		\Magento\Catalog\Model\Session $catalogSession,
		\Magento\Customer\Model\Session $customerSession,
        array $data = []
    )
    {
        $this->_categoryFactory = $categoryFactory;
		$this->registry = $registry;
		$this->categoryRepository = $categoryRepository;
		$this->_catalogSession = $catalogSession;
		$this->customerSession = $customerSession;
        parent::__construct($context, $data);
    }

    public function getCategory($categoryId) 
    {
        $this->_category = $this->_categoryFactory->create();
        $this->_category->load($categoryId);   
		
        return $this->_category;
    }
	
	public function getCurrentCategory()
    {      
        return $this->registry->registry('current_category');
    }   


    public function getChildren($categoryId = false)
    {
        if ($this->_category) {
            return $this->_category->getChildren();
			//$this->_categoryFactory->create()->load($categoryId)->getName()
        } else {
            return $this->getCategory($categoryId)->getChildren();
			//return $this->_categoryFactory->create()->load($categoryId)->getChildren()->getName();
        }        
    }
	
	public function setValue($consultora)
        {
			
            return $this->customerSession->setConsultora($consultora);
        }
	
	public function getValue() 
    {
		//return $this->_catalogSession->setConsultora($subcategoria->getName());
        //return $this->_catalogSession->setConsultora('Caramba'); 
		return $this->customerSession->getConsultora();//set value in customer session
    }
	
}


