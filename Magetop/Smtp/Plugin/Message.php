<?php
/**
 * Magetop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magetop.com license that is
 * available through the world-wide-web at this URL:
 * https://www.magetop.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magetop
 * @package     Magetop_Smtp
 * @copyright   Copyright (c) Magetop (https://www.magetop.com/)
 * @license     https://www.magetop.com/LICENSE.txt
 */

namespace Magetop\Smtp\Plugin;

use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\Template\SenderResolverInterface;
use Magento\Framework\Mail\Template\TransportBuilderByStore;
use Magetop\Smtp\Mail\Rse\Mail;

/**
 * Class Message
 * @package Magetop\Smtp\Plugin
 */
class Message
{
    /**
     * @var Mail
     */
    protected $resourceMail;

    /**
     * @var SenderResolverInterface
     */
    protected $senderResolver;

    /**
     * Message constructor.
     * @param Mail $resourceMail
     * @param SenderResolverInterface $senderResolver
     */
    public function __construct(
        Mail $resourceMail,
        SenderResolverInterface $senderResolver
    ) {
        $this->resourceMail = $resourceMail;
        $this->senderResolver = $senderResolver;
    }

    /**
     * @param TransportBuilderByStore $subject
     * @param $from
     * @param $store
     * @return array
     * @throws MailException
     */
    public function beforeSetFromByStore(TransportBuilderByStore $subject, $from, $store)
    {
        $result = $this->senderResolver->resolve($from, $store);
        $this->resourceMail->setFromByStore($result['email'], $result['name']);

        return [$from, $store];
    }
}