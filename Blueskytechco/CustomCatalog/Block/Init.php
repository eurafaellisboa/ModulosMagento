<?php
namespace Blueskytechco\CustomCatalog\Block;

class Init extends \Magento\Framework\View\Element\Template
{   

    protected $coreRegistry = null;
    protected $storeManager;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context    
     * @param \Magento\Framework\Registry                      $registry   
     * @param \Magento\Store\Model\StoreManagerInterface       $storeManager
     * @param array                                            $data       
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
        ) {
        parent::__construct($context, $data);  
        $this->coreRegistry = $registry;
        $this->storeManager = $storeManager;
    }
}
