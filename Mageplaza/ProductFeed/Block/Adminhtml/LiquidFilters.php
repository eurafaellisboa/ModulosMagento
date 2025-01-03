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

namespace Mageplaza\ProductFeed\Block\Adminhtml;

use Magento\Store\Model\StoreManagerInterface;

/**
 * Class LiquidFilters
 * @package Mageplaza\ProductFeed\Block\Adminhtml
 */
class LiquidFilters
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * LiquidFilters constructor.
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(StoreManagerInterface $storeManager)
    {
        $this->storeManager = $storeManager;
    }

    /**
     * @param $subject
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function price($subject)
    {
        return $subject . ' ' . $this->storeManager->getStore()->getCurrentCurrencyCode();
    }

    /**
     * Auto correct for csv,txt file
     *
     * @param $subject
     * @param $fieldAround
     * @param $fieldSeparate
     * @return mixed
     */
    public function mpCorrect($subject, $fieldAround, $fieldSeparate)
    {
        switch ($fieldAround) {
            case 'quotes':
                $result = str_replace('"', "'", $subject);
                break;
            case 'quote':
                $result = str_replace('"', "'", $subject);
                break;
            default:
                $result = str_replace("\n", "\t", $subject);
                switch ($fieldSeparate) {
                    case ';':
                        $result = str_replace(";", ",", $result);
                        break;
                    case ',':
                        $result = str_replace(",", ";", $result);
                        break;
                    default:
                        $result = str_replace("\t", " ", $result);
                }
        }

        return $result;
    }

    /**
     * @param $subject
     * @return int
     */
    public function count($subject)
    {
        return count($subject);
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        $filters = [
            'replace'        => ['label' => __('Replace'), 'params' => [['label' => __('Search'), 'defVal' => ''], ['label' => __('Replace'), 'defVal' => '']]],
            'replace_first'  => ['label' => __('Replace First'), 'params' => [['label' => __('Search'), 'defVal' => ''], ['label' => __('Replace'), 'defVal' => '']]],
            'slice'          => ['label' => __('Slice'), 'params' => [['label' => __('From'), 'defVal' => ''], ['label' => __('To'), 'defVal' => '']]],
            'truncate'       => ['label' => __('Truncate'), 'params' => [['label' => __('Count'), 'Chars' => ''], ['label' => __('Append Last'), 'defVal' => '']]],
            'truncatewords'  => ['label' => __('Truncate Words'), 'params' => [['label' => __('Words'), 'defVal' => ''], ['label' => __('Append Last'), 'defVal' => '']]],
            'strtolower'     => ['label' => __('Lowercase'), 'params' => []],
            'ceil'           => ['label' => __('Ceil'), 'params' => []],
            'strtoupper'     => ['label' => __('Uppercase'), 'params' => []],
            'ucfirst'        => ['label' => __('Capitalize'), 'params' => []],
            'upcase'         => ['label' => __('Uppercase'), 'params' => []],
            'ucwords'        => ['label' => __('Uppercase first character of each word '), 'params' => []],
            'append'         => ['label' => __('Append'), 'params' => [['label' => __('Append'), 'defVal' => '']]],
            'prepend'        => ['label' => __('Prepend'), 'params' => [['label' => __('Prepend'), 'defVal' => '']]],
            'at_least'       => ['label' => __('At Least'), 'params' => [['label' => __('At Least'), 'defVal' => '']]],
            'at_most'        => ['label' => __('At Most'), 'params' => [['label' => __('At Most'), 'defVal' => '']]],
            'date'           => ['label' => __('Date'), 'params' => [['label' => __('Date Format'), 'defVal' => '']]],
            'default'        => ['label' => __('Default'), 'params' => [['label' => __('Default'), 'defVal' => '']]],
            'divided_by'     => ['label' => __('Divided By'), 'params' => [['label' => __('Divided By'), 'defVal' => '']]],
            'plus'           => ['label' => __('Plus'), 'params' => [['label' => __('Plus'), 'defVal' => '']]],
            'remove'         => ['label' => __('Remove'), 'params' => [['label' => __('Remove'), 'defVal' => '']]],
            'join'           => ['label' => __('Join'), 'params' => [['label' => __('Join By'), 'defVal' => '']]],
            'minus'          => ['label' => __('Minus'), 'params' => [['label' => __('Minus'), 'defVal' => '']]],
            'modulo'         => ['label' => __('Modulo'), 'params' => [['label' => __('Divided By'), 'defVal' => '']]],
            'times'          => ['label' => __('Times'), 'params' => [['label' => __('Times'), 'defVal' => '']]],
            'abs'            => ['label' => __('Abs'), 'params' => []],
            'capitalize'     => ['label' => __('Abs'), 'params' => []],
            'downcase'       => ['label' => __('Down Case'), 'params' => []],
            'escape'         => ['label' => __('Escape'), 'params' => []],
            'escape_once'    => ['label' => __('Escape once'), 'params' => []],
            'floor'          => ['label' => __('Floor'), 'params' => []],
            'lstrip'         => ['label' => __('Left Trim'), 'params' => []],
            'newline_to_br'  => ['label' => __('Replace new line to <br'), 'params' => []],
            'reverse'        => ['label' => __('Reverse Array'), 'params' => []],
            'rstrip'         => ['label' => __('Right Trim'), 'params' => []],
            'size'           => ['label' => __('Array Size'), 'params' => []],
            'sort'           => ['label' => __('Array Sort'), 'params' => []],
            'strip'          => ['label' => __('Trim Text'), 'params' => []],
            'strip_html'     => ['label' => __('Strip Html Tags'), 'params' => []],
            'strip_newlines' => ['label' => __('Strip New Line'), 'params' => []],
            'uniq'           => ['label' => __('Unique Id Of Array'), 'params' => []],
            'url_decode'     => ['label' => __('Decode Url'), 'params' => []],
            'url_encode'     => ['label' => __('Encode Url'), 'params' => []],
        ];

        $customFilter = [
            'count'   => ['label' => __('Count'), 'params' => []],
            'price'   => ['label' => __('Price'), 'params' => []],
            'ifEmpty' => ['label' => __('If Empty'), 'params' => [['label' => __('Default'), 'defVal' => '']]],
        ];

        return array_merge($filters, $customFilter);
    }

    /**
     * @return array
     */
    public function getFiltersMethods()
    {
        return array_keys($this->getFilters());
    }

    /**
     * @param $subject
     * @param $default
     * @return mixed
     */
    public function ifEmpty($subject, $default)
    {
        if (!$subject) {
            $subject = $default;
        }

        return $subject;
    }
}
