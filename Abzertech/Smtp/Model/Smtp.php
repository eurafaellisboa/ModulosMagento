<?php

namespace Abzertech\Smtp\Model;

use Exception;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Phrase;
use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;

class Smtp extends AbstractSmtp
{

    /**
     * @param Magento\Framework\Mail\EmailMessageInterface $message
     * @throws MailException
     */
    public function sendSmtpMessage($message)
    {
        $dataHelper = $this->dataHelper;
        $dataHelper->setStoreId($this->storeModel->getStoreId());

        $message = Message::fromString($message->getRawMessage());
        
        if (!$message->getFrom()->count()) {
            $result = $this->storeModel->getFrom();
            $message->setFrom($result['email'], $result['name']);
        }
        
        //set config
        $options   = new SmtpOptions([
            'name' => $dataHelper->getConfigName(),
            'host' => $dataHelper->getConfigSmtpHost(),
            'port' => $dataHelper->getConfigSmtpPort(),
        ]);

        $connectionConfig = [];

        $auth = strtolower($dataHelper->getConfigAuth());
        if ($auth != 'none') {
            $options->setConnectionClass($auth);

            $connectionConfig = [
                'username' => $dataHelper->getConfigUsername(),
                'password' => $dataHelper->getConfigPassword()
            ];
        }

        $ssl = $dataHelper->getConfigSsl();
        if ($ssl != 'none') {
            $connectionConfig['ssl'] = $ssl;
        }

        if (!empty($connectionConfig)) {
            $options->setConnectionConfig($connectionConfig);
        }
        try {
            $transport = new SmtpTransport();
            $transport->setOptions($options);
            $transport->send($message);
            $toArr = [];
            foreach ($message->getTo() as $toAddr) {
                $toArr[] = $toAddr->getEmail();
            }
            $data['recipient'] = implode(',', $toArr);
            $data['sender'] = $message->getFrom()->rewind()->getEmail();
            $data['subject'] = $message->getSubject();
            $data['body'] = $this->escaper->escapeHtml($message->toString());
            $this->addLog($data);
        } catch (Exception $e) {
            throw new MailException(
                new Phrase($e->getMessage()),
                $e
            );
        }
    }
}
