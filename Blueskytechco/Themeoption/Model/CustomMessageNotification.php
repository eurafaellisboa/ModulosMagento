<?php
namespace Blueskytechco\Themeoption\Model;

use Magento\Framework\Notification\MessageInterface;

class CustomMessageNotification implements MessageInterface {
  const MESSAGE_IDENTITY = 'custom_system_notification';

  protected $_verifypurchasecode;

  public function __construct(
    \Blueskytechco\Themeoption\Helper\Verifypurchasecode $verifypurchasecode
  ) 
  {
      $this->_verifypurchasecode = $verifypurchasecode;
  }

  public function getIdentity()
  {
     return self::MESSAGE_IDENTITY;
  }

  public function isDisplayed()
  {
    return !$this->_verifypurchasecode->checkEnvatoPurchaseCode();
  }

  public function getText()
  {
    return '<span style="background-color:red; color:white; padding:2px 5px">'.base64_decode('WW91ciBsaWNlbnNlIGlzIGludmFsaWRhdGVkLg==').'</span> '.base64_decode('UGxlYXNlIGdvIHRvOg==').' <i>'.base64_decode('Qmx1ZXNreXRlY2hjbyA+IEFjdGl2YXRpb24gUHVyY2hhc2UgQ29kZQ==').'</i>';
  }

  public function getSeverity()
  {
    return self::SEVERITY_NOTICE;
  }
}