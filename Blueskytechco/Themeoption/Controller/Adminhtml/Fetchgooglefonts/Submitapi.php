<?php

namespace Blueskytechco\Themeoption\Controller\Adminhtml\Fetchgooglefonts;

use Magento\Framework\App\Filesystem\DirectoryList;

class Submitapi extends \Magento\Backend\App\Action
{
    protected $_googlefontsFactory;
    
    public function __construct(
        \Blueskytechco\Themeoption\Model\GooglefontsFactory $googlefontsFactory,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->_googlefontsFactory = $googlefontsFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $header   = [];
        $header[] = 'Content-length: 0';
        $header[] = 'Content-type: application/json; charset=utf-8';

        $verify_url = 'https://www.googleapis.com/webfonts/v1/webfonts?key=AIzaSyDX6p6AZ9epSHfXc1an-HTWOqI6hcFj-kE';
        $ch_verify = curl_init($verify_url);

        curl_setopt( $ch_verify, CURLOPT_HTTPHEADER, $header );
        curl_setopt( $ch_verify, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch_verify, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch_verify, CURLOPT_CONNECTTIMEOUT, 5 );
        curl_setopt( $ch_verify, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');

        $cinit_verify_data = curl_exec($ch_verify);
        curl_close( $ch_verify );
        $result = json_decode($cinit_verify_data, true);
        
        if(is_array($result) && !empty($result) && isset($result['items'])){
            $connection = $this->_googlefontsFactory->create()->getCollection()->getConnection();
            $tableName = $this->_googlefontsFactory->create()->getCollection()->getMainTable();
            $connection->truncateTable($tableName);
            foreach($result['items'] as $_item){
                if(isset($_item['family'])){
                    $this->_googlefontsFactory->create()->setData(['family' => $_item['family']])->save();
                }
            }
        }
        $this->messageManager->addSuccess(__('Fetch Google Fonts via API Successfully.'));
        $this->_redirect('adminhtml/system_config/edit/section/themeoption');
        return;
    }
}
?>