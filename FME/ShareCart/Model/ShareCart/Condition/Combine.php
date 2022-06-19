<?php

/**
 * FME Extensions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the fmeextensions.com license that is
 * available through the world-wide-web at this URL:
 * https://www.fmeextensions.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  FME
 * @author     Atta <support@fmeextensions.com>
 * @package   FME_ShareCart
 * @copyright Copyright (c) 2019 FME (http://fmeextensions.com/)
 * @license   https://fmeextensions.com/LICENSE.txt
 */

namespace FME\ShareCart\Model\ShareCart\Condition;

class Combine extends \Magento\Rule\Model\Condition\Combine
{

    /**
     * @var \FME\ShareCart\Model\Rule\Condition\ProductFactory
     */
    protected $_productFactory;

    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \FME\ShareCart\Model\Rule\Condition\ProductFactory $conditionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        array $data = []
    ) {
    
        parent::__construct($context, $data);
        $this->setType('FME\ShareCart\Model\ShareCart\Condition\Combine');
    }

    /**
     * @return array
     */
    
    /**
     * @param array $productCollection
     * @return $this
     */
    public function collectValidatedAttributes($productCollection)
    {
        foreach ($this->getConditions() as $condition) {
            /** @var Product|Combine $condition */
            $condition->collectValidatedAttributes($productCollection);
        }

        return $this;
    }
}
