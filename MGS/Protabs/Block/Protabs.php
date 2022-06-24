<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MGS\Protabs\Block;

use Magento\Framework\View\Element\Template;

/**
 * Main contact form block
 */
class Protabs extends Template
{
	/**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
	
    /**
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(Template\Context $context, array $data = [], \Magento\Framework\ObjectManagerInterface $objectManager)
    {
        parent::__construct($context, $data);
		$this->_objectManager = $objectManager;
    }
	
	public function getModel(){
		return $this->_objectManager->create('MGS\Protabs\Model\Protabs');
	}
}

