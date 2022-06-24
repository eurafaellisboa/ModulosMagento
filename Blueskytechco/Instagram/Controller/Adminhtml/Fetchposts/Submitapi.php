<?php

namespace Blueskytechco\Instagram\Controller\Adminhtml\Fetchposts;

use Magento\Framework\App\Filesystem\DirectoryList;

class Submitapi extends \Magento\Backend\App\Action
{
   protected $_instagrampostFactory;
   protected $_resourceConnection;
   protected $_indata;
   protected $_filesystem;
    
    public function __construct(
       \Blueskytechco\Instagram\Model\InstagrampostFactory $instagrampostFactory,
       \Magento\Framework\App\ResourceConnection $resourceConnection,
       \Blueskytechco\Instagram\Helper\Data $indata,
       \Magento\Framework\Filesystem $filesystem,
        \Magento\Backend\App\Action\Context $context
    ) {
       $this->_instagrampostFactory = $instagrampostFactory;
       $this->_indata = $indata;
       $this->_resourceConnection = $resourceConnection;
       $this->_filesystem = $filesystem;
        parent::__construct($context);
    }

    public function execute()
    {
       $store = $this->getRequest()->getParam('store') ? $this->getRequest()->getParam('store') : 0;
       $connection = $this->_resourceConnection->getConnection();
       $blueskytechco_instagram = $this->_resourceConnection->getTableName('blueskytechco_instagram');

       $userid = $this->_indata->getConfig('instagramsection/instagramgroup/userid', $store);
       $accesstoken = $this->_indata->getConfig('instagramsection/instagramgroup/accesstoken', $store);
       $username = $this->_indata->getConfig('instagramsection/instagramgroup/username', $store);
       $limit = ($this->_indata->getConfig('instagramsection/instagramgroup/limit', $store)) ? $this->_indata->getConfig('instagramsection/instagramgroup/limit', $store) : 10;

       if($userid == '' || $accesstoken == '' || $username == ''){
           $this->messageManager->addError(__('Error! Please enter User ID, Access Token, User Name Instagram.'));
           $this->_redirect('adminhtml/system_config/edit/section/instagramsection');
           return;
       }

       $header   = [];
        $header[] = 'Content-length: 0';
        $header[] = 'Content-type: application/json; charset=utf-8';

        $verify_url = 'https://graph.instagram.com/'.$userid.'/media';
        $ch_verify = curl_init( $verify_url . '?fields=media_url,thumbnail_url,caption,media_type,username,permalink&limit='.$limit.'&access_token='.$accesstoken);

        curl_setopt( $ch_verify, CURLOPT_HTTPHEADER, $header );
        curl_setopt( $ch_verify, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch_verify, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch_verify, CURLOPT_CONNECTTIMEOUT, 5 );
        curl_setopt( $ch_verify, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');

        $cinit_verify_data = curl_exec( $ch_verify );
        curl_close( $ch_verify );

        $result = json_decode($cinit_verify_data, true);

        $mediapath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath().'instagram';
        if(!is_dir($mediapath)){
           mkdir($mediapath);
        }
        
        if(isset($result['error']['message'])){
           $this->messageManager->addError($result['error']['message']);
           $this->_redirect('adminhtml/system_config/edit/section/instagramsection');
           return;
        }
        
        if(isset($result['data']) && is_array($result['data']) && !empty($result['data'])){
           $connection->delete(
               $blueskytechco_instagram,
               ['store = ?' => $store]
           );

           foreach ($result['data'] as $key_d => $val_d) {
               if(isset($val_d['media_type']) && $val_d['media_type'] == 'IMAGE'){
                   $instagrampost = $this->_instagrampostFactory->create();
                   if(isset($val_d['media_url'])){
                       $instagrampost->setMediaUrl($val_d['media_url']);
                       $img = $val_d['id'].'_'.rand().'.jpg';
                        $local_media_url = $mediapath.'/'.$img;
                        file_put_contents($local_media_url, file_get_contents($val_d['media_url']));
                        $instagrampost->setLocalMediaUrl('instagram/'.$img);
                   }
                   if(isset($val_d['username'])){
                       $instagrampost->setUsername($val_d['username']);
                   }
                   if(isset($val_d['permalink'])){
                       $instagrampost->setPermalink($val_d['permalink']);
                   }
                   if(isset($val_d['id'])){
                       $instagrampost->setIdInstagram($val_d['id']);
                   }
                   if(isset($val_d['caption'])){
                       $instagrampost->setCaption($val_d['caption']);
                   }
                   $instagrampost->setStore($store);
                   $instagrampost->save();
               }
           }

           $this->messageManager->addSuccess(__('Fetch Posts Via API Successfully.'));
           $this->_redirect('adminhtml/system_config/edit/section/instagramsection');
           return;
        }
        else{
           $this->messageManager->addError(__('Error! Please Try Again.'));
           $this->_redirect('adminhtml/system_config/edit/section/instagramsection');
           return;
        }

       $this->messageManager->addSuccess(__('You saved the configuration.'));
        $this->_redirect('adminhtml/system_config/edit/section/instagramsection');
    }
}
?>