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
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Mageplaza\ProductFeed\Model\Config\Source\Protocol;

/**
 * Class Delivery
 * @package Mageplaza\ProductFeed\Block\Adminhtml\Feed\Edit\Tab
 */
class Delivery extends Generic implements TabInterface
{
    /**
     * @var Enabledisable
     */
    protected $enabledisable;

    /**
     * @var Yesno
     */
    protected $yesno;

    /**
     * @var Protocol
     */
    protected $protocol;

    /**
     * Delivery constructor.
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Enabledisable $enabledisable
     * @param Yesno $yesno
     * @param Protocol $protocol
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Enabledisable $enabledisable,
        Yesno $yesno,
        Protocol $protocol,
        array $data = [])
    {
        parent::__construct($context, $registry, $formFactory, $data);

        $this->enabledisable = $enabledisable;
        $this->yesno = $yesno;
        $this->protocol = $protocol;
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

        $deliveryFieldset = $form->addFieldset('delivery_base_fieldset', [
            'legend' => __('Delivery Config'),
            'class'  => 'fieldset-wide'
        ]);
        $deliveryEnable = $deliveryFieldset->addField('delivery_enable', 'select', [
            'name'   => 'delivery_enable',
            'label'  => __('Delivery'),
            'title'  => __('Delivery'),
            'values' => $this->enabledisable->toOptionArray()
        ]);
        $protocol = $deliveryFieldset->addField('protocol', 'select', [
            'name'   => 'protocol',
            'label'  => __('Protocol'),
            'title'  => __('Protocol'),
            'values' => $this->protocol->toOptionArray()
        ]);
        $passiveMode = $deliveryFieldset->addField('passive_mode', 'select', [
            'name'   => 'passive_mode',
            'label'  => __('Passive Mode'),
            'title'  => __('Passive Mode'),
            'values' => $this->yesno->toOptionArray()
        ]);
        $hostName = $deliveryFieldset->addField('host_name', 'text', [
            'name'  => 'host_name',
            'label' => __('Host Name'),
            'title' => __('Host Name'),
            'note'  => __('It can be IP address or host name. You can add port at the end of host name. E.g: ftp.domain.com:22'),
        ]);
        $userName = $deliveryFieldset->addField('user_name', 'text', [
            'name'  => 'user_name',
            'label' => __('User Name'),
            'title' => __('User Name'),
        ]);
        $password = $deliveryFieldset->addField('password', 'password', [
            'name'  => 'password',
            'label' => __('Password'),
            'title' => __('Password'),
        ]);
        $directory = $deliveryFieldset->addField('directory_path', 'text', [
            'name'  => 'directory_path',
            'label' => __('Directory Path'),
            'title' => __('Directory Path'),
            'note'  => __('Full path of a directory. E.g: /var/www/path/to/your-folder/'),
        ]);
        $testConnect = $deliveryFieldset->addField('test_connect', 'button', [
            'name'               => 'test_connect',
            'value'              => __('Test Connection'),
            'after_element_html' => '<div class="test-connect-message"></div>'
        ]);
        $this->setChild('form_after',
            $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Form\Element\Dependence')
                ->addFieldMap($protocol->getHtmlId(), $protocol->getName())
                ->addFieldMap($passiveMode->getHtmlId(), $passiveMode->getName())
                ->addFieldMap($hostName->getHtmlId(), $hostName->getName())
                ->addFieldMap($userName->getHtmlId(), $userName->getName())
                ->addFieldMap($directory->getHtmlId(), $directory->getName())
                ->addFieldMap($password->getHtmlId(), $password->getName())
                ->addFieldMap($deliveryEnable->getHtmlId(), $deliveryEnable->getName())
                ->addFieldMap($testConnect->getHtmlId(), $testConnect->getName())
                ->addFieldDependence($protocol->getName(), $deliveryEnable->getName(), '1')
                ->addFieldDependence($passiveMode->getName(), $deliveryEnable->getName(), '1')
                ->addFieldDependence($passiveMode->getName(), $protocol->getName(), 'ftp')
                ->addFieldDependence($hostName->getName(), $deliveryEnable->getName(), '1')
                ->addFieldDependence($userName->getName(), $deliveryEnable->getName(), '1')
                ->addFieldDependence($password->getName(), $deliveryEnable->getName(), '1')
                ->addFieldDependence($directory->getName(), $deliveryEnable->getName(), '1')
                ->addFieldDependence($testConnect->getName(), $deliveryEnable->getName(), '1')
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
        return __('Delivery');
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
