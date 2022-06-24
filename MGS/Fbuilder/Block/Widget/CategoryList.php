<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MGS\Fbuilder\Block\Widget;

use Magento\Catalog\Model\Category;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\View\Element\Template;
/**
 * Main contact form block
 */
class CategoryList extends Template
{
	protected $_categoryCollection;
	protected $_file;
	protected $_filesystem;
	
	public function __construct(
		Template\Context $context,
		\Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollection,
		\Magento\Framework\Filesystem\Driver\File $file,
		array $data = []
	){
		$this->_categoryCollection = $categoryCollection;
		$this->_file = $file;
		$this->_filesystem = $context->getFilesystem();
        parent::__construct($context, $data);
		
    }
	
	public function getCategoryByIds(){
		$result = [];
		if($this->hasData('category_ids')){
			$categoryIds = $this->getData('category_ids');
			$categoryArray = explode(',',$categoryIds);
			
			if(count($categoryArray)>0){				
				$result = $this->_categoryCollection->create()
					->addAttributeToSelect(['name', 'fbuilder_thumbnail', 'fbuilder_icon', 'fbuilder_font_class'])
					->addAttributeToFilter('entity_id',['in' => $categoryArray])
					->addAttributeToFilter('is_active','1');
			}
		}
		return $result;
	}
	
	public function getCategoryImageHtml($category){
		if($category->getFbuilderThumbnail()!=''){
			$filePath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('catalog/category/') . $category->getFbuilderThumbnail();
			if ($this->_file->isExists($filePath))  {
				return '<img src="'.$this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]).'catalog/category/'.$category->getFbuilderThumbnail().'" alt=""/>';
			}
		}
		return;
	}
	
	public function getCategoryIconHtml($category){
		if($category->getFbuilderFontClass()!=''){
			return '<span class="category-icon font-icon '.$category->getFbuilderFontClass().'"></span>';
		}else{
			if($category->getFbuilderIcon()!=''){
				$filePath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('catalog/category/') . $category->getFbuilderIcon();
				if ($this->_file->isExists($filePath))  {
					return '<span class="category-icon"><img src="'.$this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]).'catalog/category/'.$category->getFbuilderIcon().'" alt=""/></span>';
				}
			}
		}
		
		return;
	}
}

