<?php

namespace Abzertech\Smtp\Block\Adminhtml;

use Exception;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Validator\EmailAddress;
use Abzertech\Smtp\Helper\Data;
use Abzertech\Smtp\Model\Email;
use Zend_Mail;
use Zend_Mail_Exception;
use Zend_Mail_Transport_Smtp;
use Zend_Validate;
use Zend_Validate_Exception;

class EmailTest extends Template
{

    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * @var Email
     */
    protected $email;

    /**
     * @var toAddress
     */
    protected $toAddress;

    /**
     * @var fromAddress
     */
    protected $fromAddress;

    /**
     * @var storeId
     */
    protected $storeId;

    /**
     * @var hash
     */
    protected $hash;

    /**
     * Remove values from global post and store values locally
     * @var configFields array
     */
    protected $configFields = [
        'active' => '',
        'auth' => '',
        'protocol' => '',
        'host' => '',
        'port' => '',
        'username' => '',
        'password' => '',
        'to_email' => '',
        'from_email' => ''
    ];

    /**
     * @var EmailAddress
     */
    protected $emailAddressValidator;

    /**
     * EmailTest constructor.
     *
     * @param Context $context
     * @param Data $dataHelper
     * @param Email $email
     * @param EmailAddress $emailAddressValidator
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $dataHelper,
        Email $email,
        EmailAddress $emailAddressValidator,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->dataHelper = $dataHelper;
        $this->email = $email;
        $this->emailAddressValidator = $emailAddressValidator;

        $this->init();
    }

    /**
     * @param $id
     * @return $this
     */
    public function setStoreId($id)
    {
        $this->storeId = $id;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getStoreId()
    {
        return $this->storeId;
    }

    /**
     * @param null $key
     * @return array|string
     */
    public function getConfig($key = null)
    {
        if ($key === null) {
            return $this->configFields;
        } elseif (!array_key_exists($key, $this->configFields)) {
            return '';
        } else {
            return $this->configFields[$key];
        }
    }

    /**
     * @param null $key
     * @param string $value
     * @return array|string
     */
    public function setConfig($key, $value = null)
    {
        if (array_key_exists($key, $this->configFields)) {
            $this->configFields[$key] = $value;
        }
        return $this;
    }

    /**
     * Load default config
     *
     * @return $this
     */
    public function loadDefaultConfig()
    {
        $request = $this->getRequest();
        $formPostArray = (array) $request->getPost();

        $fields = array_keys($this->configFields);
        foreach ($fields as $field) {
            if (!array_key_exists($field, $formPostArray)) {
                $this->setConfig($field, $this->dataHelper->getConfigValue($field), $this->getStoreId());
            } else {
                $this->setConfig($field, $request->getPost($field));
            }
        }

        //if password mask (6 stars)
        if ($this->getConfig('password') === '******') {
            $password = $this->dataHelper->getConfigPassword($this->getStoreId());
            $this->setConfig('password', $password);
        }

        return $this;
    }

    /**
     * init default config
     *
     * @return void
     */
    protected function init()
    {
        $request = $this->getRequest();
        $this->setStoreId($request->getParam('store', null));

        $this->loadDefaultConfig();

        $this->toAddress = $this->getConfig('to_email') ? $this->getConfig('to_email') : $this->getConfig('username');

        $this->fromAddress = trim($this->getConfig('from_email'));

        if (!$this->emailAddressValidator->isValid($this->fromAddress)) {
            $this->fromAddress = $this->toAddress;
        }

        $this->hash = time() . '.' . rand(300000, 900000);
    }

    /**
     * verify config
     *
     * @return array
     */
    public function verify()
    {

        $settings = [
            'server_email' => 'validateServerEmailSetting',
            'module_email_setting' => 'validateModuleEmailStatus',
        ];

        $result = $this->error();
        $hasError = false;

        foreach ($settings as $functionName) {
            $result = call_user_func([$this, $functionName]);

            if (array_key_exists('has_error', $result)) {
                if ($result['has_error'] === true) {
                    $hasError = true;
                    break;
                }
            } else {
                $hasError = true;
                $result = $this->error(true, 'MP103 - Unknown Error');
                break;
            }
        }

        if (!$hasError) {
            $result['msg'] = __('Please check your email') . ' ' . $this->toAddress;
        }
        return [$result];
    }

    /**
     * Validate Server Email Setting
     *
     * @return array
     * @throws Zend_Mail_Exception
     * @throws Zend_Validate_Exception
     */
    protected function validateServerEmailSetting()
    {
        $request = $this->getRequest();
        $username = $this->getConfig('username');
        $password = $this->getConfig('password');
        $auth = strtolower($this->getConfig('auth'));
       
        if (!$request->getParam('store', false)) {
            if ($auth != 'none' && (empty($username) || empty($password))) {
                return $this->error(
                    true,
                    __('Please enter a valid username/password')
                );
            }
        }

        //SMTP server configuration
        $smtpHost = $this->getConfig('host');

        $smtpConf = [
            'name' => 'localhost',
            'port' => $this->getConfig('port')
        ];

        if ($auth != 'none') {
            $smtpConf['auth'] = $auth;
            $smtpConf['username'] = $username;
            $smtpConf['password'] = $password;
        }

        $ssl = $this->getConfig('protocol');
        if ($ssl != 'none') {
            $smtpConf['ssl'] = $ssl;
        }

        $transport = new Zend_Mail_Transport_Smtp($smtpHost, $smtpConf);

        $from = trim($this->getConfig('from_email'));
        $from = Zend_Validate::is($from, 'EmailAddress') ? $from : $username;
        $this->fromAddress = $from;

        //Create email
        $name = 'Test from Abzer SMTP';
        $mail = new Zend_Mail();
        $mail->setFrom($this->fromAddress, $name);
        $mail->addTo($this->toAddress, $this->toAddress);
        $mail->setSubject('Hello from Abzer SMTP');
        
        $htmlBody = $this->email->setTemplateVars(['hash' => $this->hash])->getEmailBody();

        $mail->setBodyHtml($htmlBody);

        $result = $this->error();

        try {
            if (!$mail->send($transport) instanceof Zend_Mail) {
            }
        } catch (Exception $e) {
            $result = $this->error(true, __($e->getMessage()));
        }

        return $result;
    }

    /**
     * Validate Module Email Status
     *
     * @return array
     */
    public function validateModuleEmailStatus()
    {
        $result = $this->error();

        if (!$this->getConfig('active')) {
            $result = $this->error(
                true,
                __('SMTP module is not enabled')
            );
        }

        return $result;
    }

    /**
     * Format error message
     *
     * @param string $s
     * @return string
     */
    public function formatErrorMsg($s)
    {
        return preg_replace(
            '@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@',
            '<a href="$1" target="_blank">$1</a>',
            nl2br($s)
        );
    }

    /**
     * error message
     *
     * @param bool $hasError
     * @param string $msg
     * @return array
     */
    public function error($hasError = false, $msg = '')
    {
        return [
            'has_error' => (bool) $hasError,
            'msg' => (string) $msg
        ];
    }
}
