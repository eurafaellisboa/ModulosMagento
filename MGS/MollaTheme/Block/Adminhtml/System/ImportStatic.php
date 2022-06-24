<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MGS\MollaTheme\Block\Adminhtml\System;

use Magento\Framework\App\Filesystem\DirectoryList;
/**
 * Export CSV button for shipping table rates
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class ImportStatic extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $_backendUrl;
	
	protected $collectionFactory;
	
    /**
     * @param \Magento\Framework\Data\Form\Element\Factory $factoryElement
     * @param \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\Filesystem $filesystem
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Data\Form\Element\Factory $factoryElement,
        \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection,
        \Magento\Framework\Escaper $escaper,
        \Magento\Backend\Model\UrlInterface $backendUrl,
		\Magento\Framework\Filesystem $filesystem,
        array $data = []
    ) {
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
        $this->_backendUrl = $backendUrl;
		$this->_filesystem = $filesystem;
    }
	
	public function isLocalhost() {
        $whitelist = array(
            '127.0.0.1',
			'localhost',
			'127.0.0.1:8080',
			'localhost:8080',
            '::1'
        );
        
        return in_array($_SERVER['REMOTE_ADDR'], $whitelist);
    }

    /**
     * @return string
     */
    public function getElementHtml()
    {
		$html = '';
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$activeKey = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('active_theme/activate/molla');
		$dir = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('mgs/static_blocks');
		$filePart = pathinfo($dir.'/molla.xml');
		if(isset($filePart['extension']) && $filePart['extension']=='xml'){
			$url = $this->_backendUrl->getUrl("adminhtml/mollatheme/importstatic", ['theme'=>'molla', 'activate'=>'25724801']);
			if($activeKey!='' || $this->isLocalhost()){
				$html .= '<button type="button" class="action-default scalable" onclick="setLocation(\''.$url.'\')" data-ui-id="widget-button-2" style="margin-bottom:10px"><span style="text-transform: capitalize;">'.__("Import").'</span></button>';
			}else{
				$html .= '<button type="button" class="action-default scalable" onclick="return false" data-ui-id="widget-button-2" style="margin-bottom:10px" disabled="disabled"><span style="text-transform: capitalize;">'.__("Import").'</span></button><a href="'.$this->_backendUrl->getUrl('adminhtml/system_config/edit/section/active_theme').'" style="text-decoration:none; margin-left:10px"><span style="color:#ff0000">'.__('Activation is required.').'</span></a>';
			}
		}else{
			$html .= '<span style="margin-top:5px; display:block">'.__('Have no static block to import').'</span>';
		}

        return $html;
    }
}
