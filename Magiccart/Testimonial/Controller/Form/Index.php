<?php
/*
 * Magepow
 * @category: Magepow
 * @copyright: Copyright (c) 2014 Magepow (http://www.magepow.com/)
 * @license: http://www.magepow.com/license-agreement
 * @Author: DavidDuong
 * @@Create Date: Tuesday, June 18th 2019, 10:43:18 pm
 * @@Modified By: DavidDuong
 * @@Modify Date: Friday, July 5th 2019, 1:18:27 pm
 */

namespace Magiccart\Testimonial\Controller\Form;

abstract class Index extends \Magiccart\Testimonial\Controller\Form {
	/**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context
    ) {
        parent::__construct($context);
    }

    /**
     * Renders CMS Home page
     *
     * @param string|null $coreRoute
     * @return \Magento\Framework\Controller\Result\Forward
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($coreRoute = null)
    {
        $this->_view->loadLayout();
        $this->_view->getLayout()->getBlock('testimonialform');
        $this->_view->renderLayout();
    }
}
