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
namespace FME\ShareCart\Controller\Adminhtml\Sharecart;

use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use FME\ShareCart\Model\Sharecart;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\TestFramework\Inspection\Exception;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\RequestInterface;

class Save extends \Magento\Backend\App\Action
{
    protected $dataPersistor;
    protected $scopeConfig;
    protected $_escaper;
    protected $inlineTranslation;
    protected $_dateFactory;
    protected $uploaderPool;

    public function __construct(
        Context $context,
        DataPersistorInterface $dataPersistor,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateFactory
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->scopeConfig = $scopeConfig;
        $this->_escaper = $escaper;
        $this->_dateFactory = $dateFactory;
        $this->inlineTranslation = $inlineTranslation;
        parent::__construct($context);
    }

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
    //print_r($data);exit;
        if ($data) {
            $id = $this->getRequest()->getParam('sharecart_id');
          
            if (empty($data['sharecart_id'])) {
                $data['sharecart_id'] = null;
            }
            /** @var \Magento\Cms\Model\Block $model */
            $model = $this->_objectManager->create('FME\ShareCart\Model\Sharecart')->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addError(__('This Testimonials no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }
            $model->setData($data);
            $this->inlineTranslation->suspend();
            try {
                $model->save();
                $this->messageManager->addSuccess(__('ShareCart Saved successfully'));
                $this->dataPersistor->clear('fme_sharecart');

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['sharecart_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Testimonials.'));
            }

            $this->dataPersistor->set('fme_sharecart', $data);
            return $resultRedirect->setPath(
                '*/*/edit',
                ['sharecart_id' => $this->getRequest()->getParam('sharecart_id')]
            );
        }
        return $resultRedirect->setPath('*/*/');
    }

    protected function getUploader($type)
    {
        return $this->uploaderPool->getUploader($type);
    }
}
