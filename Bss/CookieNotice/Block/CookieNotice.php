<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_CookieNotice
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\CookieNotice\Block;

use \Bss\CookieNotice\Model\Config\Source\Position;

class CookieNotice extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Bss\CookieNotice\Helper\Data
     */
    protected $helper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Bss\CookieNotice\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Bss\CookieNotice\Helper\Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve Module Enable
     *
     * @return bool
     */
    public function isEnable()
    {
        return $this->helper->isEnable();
    }

    /**
     * Get Auto Hide Message
     *
     * @return int
     */
    public function getHideMsg()
    {
        $miliseconds = $this->helper->getHideMsg();
        $seconds = $miliseconds * 1000;
        return $seconds;
    }

    /**
     * Get Position
     *
     * @return string
     */
    public function getPosition()
    {
        $position = $this->helper->getPosition();
        switch ($position) {
            case Position::POSITION_BOTTOM_LEFT:
                return json_encode(
                    ['left' => 0,'bottom' => 0]
                );
            case Position::POSITION_TOP_LEFT:
                return json_encode(
                    ['left' => 0,'top' => 0]
                );
            case Position::POSITION_BOTTOM_RIGHT:
                return json_encode(
                    ['right' => 0,'bottom' => 0]
                );
            case Position::POSITION_TOP_RIGHT:
                return json_encode(
                    ['right' => 0,'top' => 0]
                );
        }
    }

    /**
     * Get Message Title
     *
     * @return string
     */
    public function getMsgTitle()
    {
        return $this->helper->getMsgTitle();
    }

    /**
     * Get Title Text Color
     *
     * @return string
     */
    public function getColorTitle()
    {
        return $this->helper->getColorTitle();
    }

    /**
     * Get Message
     *
     * @return string
     */
    public function getMsgContent()
    {
        return $this->helper->getMsgContent();
    }

    /**
     * Get Message Text Color
     *
     * @return string
     */
    public function getColorContent()
    {
        return $this->helper->getColorContent();
    }

    /**
     * Get Background Color
     *
     * @return string
     */
    public function getColorBg()
    {
        return $this->helper->getColorBg();
    }

    /**
     * Get Text Acceptance Button
     *
     * @return string
     */
    public function getTextBtnAccept()
    {
        return $this->helper->getTextBtnAccept();
    }

    /**
     * Get Text Color Acceptance Button
     *
     * @return string
     */
    public function getColorBtnAccept()
    {
        return $this->helper->getColorBtnAccept();
    }

    /**
     * Get Background Color Acceptance Button
     *
     * @return string
     */
    public function getColorBgBtnAccept()
    {
        return $this->helper->getColorBgBtnAccept();
    }

    /**
     * Get Text More Information Button
     *
     * @return string
     */
    public function getTextBtnMoreInfor()
    {
        return $this->helper->getTextBtnMoreInfor();
    }

    /**
     * Get Text Color More Information Button
     *
     * @return string
     */
    public function getColorBtnMoreInfor()
    {
        return $this->helper->getColorBtnMoreInfor();
    }

    /**
     * Get Background Color More Information Button
     *
     * @return string
     */
    public function getColorBgBtnMoreInfor()
    {
        return $this->helper->getColorBgBtnMoreInfor();
    }

    /**
     * Get CMS Page
     *
     * @return string
     */
    public function getCMSPage()
    {
        $identifier = $this->helper->getCMSPage();
        $baseUrl = $this->getBaseUrl();
        $pageUrl = $baseUrl.$identifier;
        return $pageUrl;
    }

    /**
     * Get Custom Css
     *
     * @return string
     */
    public function getCustomStyle()
    {
        return $this->helper->getCustomStyle();
    }
}
