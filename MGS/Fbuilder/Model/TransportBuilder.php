<?php
/**
 * Mail Template Transport Builder
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MGS\Fbuilder\Model;
 
use Magento\Framework\Mail\MessageInterface;
use Magento\Framework\Mail\TransportInterfaceFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Mail\Template\FactoryInterface;
use Magento\Framework\Mail\Template\SenderResolverInterface;


class TransportBuilder extends \Magento\Framework\Mail\Template\TransportBuilder {
	
    protected $subject;
    protected $content;
	
    public function __construct(FactoryInterface $templateFactory,
        MessageInterface $message,
        SenderResolverInterface $senderResolver,
        ObjectManagerInterface $objectManager,
        TransportInterfaceFactory $mailTransportFactory) {

        parent::__construct($templateFactory,
            $message,
            $senderResolver,
            $objectManager,
            $mailTransportFactory);
    }

    public function attachFile($file, $name) {
        if (!empty($file) && file_exists($file)) {
            $this->message
            ->createAttachment(
                file_get_contents($file),
                \Zend_Mime::TYPE_OCTETSTREAM,
                \Zend_Mime::DISPOSITION_ATTACHMENT,
                \Zend_Mime::ENCODING_BASE64,
                basename($name)
                );

            return $this;
        }

        return false;
    }
	
	public function setSubject($suject)
    {
        $this->subject = $suject;
        return $this;
    }
	
	public function setBodyHtml($content)
    {
        $this->content = $content;
        return $this;
    }
	
	/**
     * Prepare message
     *
     * @return $this
     */
    protected function prepareMessage()
    {
        $this->message->setMessageType('html')
            ->setBody($this->content)
            ->setSubject(html_entity_decode($this->subject, ENT_QUOTES));

        return $this;
    }

}