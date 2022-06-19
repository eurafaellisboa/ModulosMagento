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
namespace FME\ShareCart\Plugin;
 
class ChangeName extends \Magento\Checkout\Controller\Cart\Index 
{

    public function afterExecute(
        \Magento\Checkout\Controller\Cart\Index $cartIndex,
        $resultPage
    )
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('TITLE TAG CONTENT HERE'));
    
        $titleBlock = $resultPage->getLayout()->getBlock('page.main.title');
        if ($titleBlock) {
            $titleBlock->setPageTitle(
                __('mycustompage')
            );
        }
        return $resultPage;
    }

 }
