<?php
/**
 * Class AjaxLayeredNavigation
 *
 * PHP version 7
 *
 * @category Sparsh
 * @package  Sparsh_AjaxLayeredNavigation
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
namespace Sparsh\AjaxLayeredNavigation\Plugin\Category;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\Page;

/**
 * Class View
 *
 * @category Sparsh
 * @package  Sparsh_AjaxLayeredNavigation
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
class View
{
    /**
     * @var JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * View constructor.
     *
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(JsonFactory $resultJsonFactory)
    {
        $this->_resultJsonFactory = $resultJsonFactory;
    }

    /**
     * AroundExecute
     *
     * @param  \Magento\Catalog\Controller\Category\View $subject
     * @param  \Closure                                  $method
     * @return \Magento\Framework\Controller\Result\Json|mixed
     */
    public function aroundExecute(\Magento\Catalog\Controller\Category\View $subject, \Closure
        $method
    )
    {
        $response = $method();
        if ($response instanceof Page) {
            if ($subject->getRequest()->getParam('ajax') == 1) {
                $subject->getRequest()->getQuery()->set('ajax', null);
                $requestUri = $subject->getRequest()->getRequestUri();
                $requestUri = preg_replace('/(\?|&)ajax=1/', '', $requestUri);
                $subject->getRequest()->setRequestUri($requestUri);

                $productsblockhtml = $response->getLayout()->getBlock('category.products')->toHtml();
                $leftnavblockhtml = $response->getLayout()->getBlock('catalog.leftnav')->toHtml();
                return $this->_resultJsonFactory->create()->setData(
                    ['success' => true, 'html' => [
                                'products_list' => $productsblockhtml,
                                'filters' => $leftnavblockhtml
                    ]]
                );
            }
        }
        return $response;
    }
}
