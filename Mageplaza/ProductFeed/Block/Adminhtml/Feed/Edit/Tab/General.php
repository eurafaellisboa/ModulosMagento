<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_ProductFeed
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ProductFeed\Block\Adminhtml\Feed\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Config\Model\Config\Source\Enabledisable;
use Magento\Cron\Model\Config\Source\Frequency;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Store\Model\System\Store;
use Mageplaza\ProductFeed\Helper\Data;
use Mageplaza\ProductFeed\Model\Config\Source\DaysOfMonth;
use Mageplaza\ProductFeed\Model\Config\Source\DaysOfWeek;
use Mageplaza\ProductFeed\Model\Config\Source\ExecutionMode;
use Mageplaza\ProductFeed\Model\ResourceModel\History\CollectionFactory;

/**
 * Class General
 * @package Mageplaza\ProductFeed\Block\Adminhtml\Feed\Edit\Tab
 */
class General extends Generic implements TabInterface
{
    /**
     * @var Enabledisable
     */
    protected $enabledisable;

    /**
     * @var Store
     */
    protected $systemStore;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var Frequency
     */
    protected $frequency;

    /**
     * @var ExecutionMode
     */
    protected $executionMode;

    /**
     * @var DaysOfWeek
     */
    protected $daysOfWeek;

    /**
     * @var DaysOfMonth
     */
    protected $daysOfMonth;

    /**
     * General constructor.
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Enabledisable $enableDisable
     * @param Store $systemStore
     * @param Data $helperData
     * @param CollectionFactory $collectionFactory
     * @param Frequency $frequency
     * @param ExecutionMode $executionMode
     * @param DaysOfWeek $daysOfWeek
     * @param DaysOfMonth $daysOfMonth
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Enabledisable $enableDisable,
        Store $systemStore,
        Data $helperData,
        CollectionFactory $collectionFactory,
        Frequency $frequency,
        ExecutionMode $executionMode,
        DaysOfWeek $daysOfWeek,
        DaysOfMonth $daysOfMonth,
        array $data = []
    )
    {
        parent::__construct($context, $registry, $formFactory, $data);

        $this->enabledisable     = $enableDisable;
        $this->systemStore       = $systemStore;
        $this->helperData        = $helperData;
        $this->collectionFactory = $collectionFactory;
        $this->frequency         = $frequency;
        $this->executionMode     = $executionMode;
        $this->daysOfWeek        = $daysOfWeek;
        $this->daysOfMonth       = $daysOfMonth;
    }

    /**
     * @inheritdoc
     */
    protected function _prepareForm()
    {
        /** @var \Mageplaza\ProductFeed\Model\Feed $feed */
        $feed = $this->_coreRegistry->registry('mageplaza_productfeed_feed');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('feed_');
        $form->setFieldNameSuffix('feed');

        $fieldset = $form->addFieldset('base_fieldset', [
            'legend' => __('General Information'),
            'class'  => 'fieldset-wide'
        ]);

        $fieldset->addField('name', 'text', [
            'name'     => 'name',
            'label'    => __('Name'),
            'title'    => __('Name'),
            'required' => true
        ]);

        $fieldset->addField('status', 'select', [
            'name'   => 'status',
            'label'  => __('Status'),
            'title'  => __('Status'),
            'values' => $this->enabledisable->toOptionArray()
        ]);

        /** @var \Magento\Framework\Data\Form\Element\Renderer\RendererInterface $rendererBlock */
        $rendererBlock = $this->getLayout()->createBlock('Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element');
        $fieldset->addField('store_id', 'select', [
            'name'   => 'store_id',
            'label'  => __('Store Views'),
            'title'  => __('Store Views'),
            'values' => $this->systemStore->getStoreValuesForForm(false, true)
        ])->setRenderer($rendererBlock);

        $fieldset->addField('file_name', 'text', [
            'name'     => 'file_name',
            'label'    => __('File Name'),
            'title'    => __('File Name'),
            'required' => true
        ]);

        if (($feedId = $feed->getId()) && ($feed->getId() !== 'copy')) {
            $historyCollection = $this->collectionFactory->create()
                ->addFieldToFilter('feed_id', $feedId)
                ->setOrder('created_at', 'desc');
            if ($historyCollection->getSize()) {
                $fieldset->addField('feed_id', 'hidden', ['name' => 'feed_id']);

                $history = $historyCollection->getFirstItem();
                $fileUrl = $this->helperData->getFileUrl($history->getFile());

                $fieldset->addField('file_url', 'link', [
                    'name'  => 'file_url',
                    'href'  => $fileUrl,
                    'label' => __('Generated File URL'),
                    'title' => __('Generated File URL'),
                    'value' => $fileUrl
                ]);
                $fieldset->addField('product_count', 'label', [
                    'name'  => 'product_count',
                    'label' => __('Number of exported Products'),
                    'title' => __('Number of exported Products'),
                    'value' => $history->getProductCount()
                ]);
                $fieldset->addField('generated_on', 'label', [
                    'name'  => 'generated_on',
                    'label' => __('Generated On'),
                    'title' => __('Generated On'),
                    'value' => $this->helperData->convertToLocaleTime($history->getCreatedAt()),
                ]);
                $fieldset->addField('error_message', 'label', [
                    'name'               => 'error_message',
                    'value'              => $history->getErrorMessage(),
                    'after_element_html' => '<style>.field-error_message{color: red}</style>'
                ]);
            }
        }

        $generateFieldset = $form->addFieldset('delivery_fieldset', [
            'legend' => __('Generate Config'),
            'class'  => 'fieldset-wide'
        ]);

        $executionMode     = $generateFieldset->addField('execution_mode', 'select', [
            'name'   => 'execution_mode',
            'label'  => __('Execution Mode'),
            'title'  => __('Execution Mode'),
            'values' => $this->executionMode->toOptionArray(),
            'note'   => __('Select <b>Cron</b> to generate the feed automatically. Select <b>Manual</b> to generate the feed manually'),
        ]);
        $frequency         = $generateFieldset->addField('frequency', 'select', [
            'name'   => 'frequency',
            'label'  => __('Frequency'),
            'title'  => __('Frequency'),
            'values' => $this->frequency->toOptionArray(),
            'note'   => __('How often the feed is generated')
        ]);
        $cronRunDayOfWeek  = $generateFieldset->addField('cron_run_day_of_week', 'select', [
            'name'   => 'cron_run_day_of_week',
            'label'  => __('Day'),
            'title'  => __('Day'),
            'values' => $this->daysOfWeek->toOptionArray(),
            'note'   => __('Day of week')
        ]);
        $cronRunDayOfMonth = $generateFieldset->addField('cron_run_day_of_month', 'select', [
            'name'   => 'cron_run_day_of_month',
            'label'  => __('Date'),
            'title'  => __('Date'),
            'values' => $this->daysOfMonth->toOptionArray(),
            'note'   => __('Date of month')
        ]);
        $cronRunTime       = $generateFieldset->addField('cron_run_time', '\Mageplaza\ProductFeed\Block\Adminhtml\Feed\Edit\Tab\Renderer\Time', [
            'name'  => 'cron_run_time',
            'label' => __('Cron Run Time'),
            'title' => __('Cron Run Time'),
            'note'  => __('Time zone UTC')
        ]);

        $this->setChild('form_after',
            $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Form\Element\Dependence')
                ->addFieldMap($frequency->getHtmlId(), $frequency->getName())
                ->addFieldMap($cronRunTime->getHtmlId(), $cronRunTime->getName())
                ->addFieldMap($executionMode->getHtmlId(), $executionMode->getName())
                ->addFieldMap($cronRunDayOfWeek->getHtmlId(), $cronRunDayOfWeek->getName())
                ->addFieldMap($cronRunDayOfMonth->getHtmlId(), $cronRunDayOfMonth->getName())
                ->addFieldDependence($frequency->getName(), $executionMode->getName(), 'cron')
                ->addFieldDependence($cronRunTime->getName(), $executionMode->getName(), 'cron')
                ->addFieldDependence($cronRunDayOfWeek->getName(), $executionMode->getName(), 'cron')
                ->addFieldDependence($cronRunDayOfWeek->getName(), $frequency->getName(), 'W')
                ->addFieldDependence($cronRunDayOfMonth->getName(), $executionMode->getName(), 'cron')
                ->addFieldDependence($cronRunDayOfMonth->getName(), $frequency->getName(), 'M')
        );

        $form->addValues($feed->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('General');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}
