<?php
namespace MGS\Amp\Helper;

class Configurable extends \MGS\Amp\Helper\Config {	
	protected $productRepository; 
	
	/**
     * @param \Magento\Framework\View\Element\Context    	$context
     * @param \Magento\Store\Model\StoreManagerInterface 	$storeManager
     * @param \Magento\Framework\App\Request\Http		 	$request
     * @param \Magento\Framework\Url					 	$urlBuilder
     */
	public function __construct(
		\Magento\Framework\View\Element\Context $context,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\App\Request\Http $request,
		\Magento\Framework\Url $urlBuilder,
		\Magento\Framework\DomDocument\DomDocumentFactory $domFactory,
		\Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
		\Magento\Swatches\Helper\Data $swatchHelper
	) {
		parent::__construct($context, $storeManager, $request, $urlBuilder, $domFactory);
		$this->productRepository = $productRepository;
		$this->swatchHelper = $swatchHelper;
	}
	
	public function getAtributeSwatchHashcode($optionid) {
		$hashcodeData = $this->swatchHelper->getSwatchesByOptionsId([$optionid]);
		return $hashcodeData[$optionid]['value'];
	}
	
	public function getAmpJsonOption($product, $formInfo){
		$html = '';
		if($product->getTypeId()=='configurable'){
			$data = $product->getTypeInstance()->getConfigurableOptions($product);
			$options = $result = array();
			
			$attributes = [];
			$i=1;
			
			foreach($data as $attributeCode => $attribute){
				 foreach($attribute as $p){
					 $attributes[$i] = $p['attribute_code'];
					 $result[$attributeCode]['attribute_code'] = $p['attribute_code'];
					 $result[$attributeCode]['attribute_label'] = $p['super_attribute_label'];
					 $result[$attributeCode]['option'][$p['value_index']]['label'] = $p['option_title'];
					 $result[$attributeCode]['option'][$p['value_index']]['product'][] = $p['sku'];
				 }
				 $i++;
			}
			if(count($data)==2){
				$html .= $this->getJsonTwoOption($result, $attributes, $product->getId());
			}elseif(count($data)==1){
				$html .= $this->getJsonOneOption($result, $product->getId());
			}else{
				$html .= '<a href="'.$this->getCanonicalUrl().'" class="btn-cart">'.__('Choose Option').'</a>';
			}
		}
		return $html;
	}

	public function getJsonOneOption($data, $productId){
		$html = '';
		$imageHelper =  \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Catalog\Helper\Image');
		$htmljSon = '<amp-state id="productImage'.$productId.'"><script type="application/json">{';
		foreach($data as $attributeId=>$option){
			$html .= '<div class="'.str_replace('_','',$option['attribute_code']).'-selector attribute-selector"><label for="'.str_replace('_','',$option['attribute_code']).'">'.$option['attribute_label'].': <span data-text="optionText'.$option['attribute_code'].$productId.'  ? optionText'.$option['attribute_code'].$productId.' : \'\'"></span></label><amp-selector name="super_attribute['.$attributeId.']" layout="container" on="select:AMP.setState({imageUrl'.$productId.': productImage'.$productId.'[event.targetOption].image})"><ul>';
			foreach($option['option'] as $optionId=>$optionInfo){
				$html .= '<li option="'.$optionId.'" on="tap:AMP.setState({optionText'.$option['attribute_code'].$productId.': \''.$optionInfo['label'].'\'})" role="option" tabindex="0">';
				
				$htmljSon .= '"'.$optionId.'":{';
				
				$optionVisual = $this->getAtributeSwatchHashcode($optionId);
				$bg = $img = false;
				if (strpos($optionVisual, '#') !== false) {
					$bg = true;
				}elseif (strpos($optionVisual, '/') !== false) {
					$img = true;
				}
				if($bg){
					$html .= '<span class="option-background" style="background:'.$optionVisual.'" data-swatches="'.$optionVisual.'"></span>';
				}
				if($img){
					$html .= '<span class="option-image"><amp-img class="attribute-image" layout="responsive" src="'.$this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]).'attribute/swatch'.$optionVisual.'" width="28" height="28" alt=""></amp-img></span>';
				}
				if(!$bg && !$img){
					$html .= '<span class="option-text">'.$optionInfo['label'].'</span>';
				}
				
				if(isset($optionInfo['product']) && count($optionInfo['product'])>0){
					foreach($optionInfo['product'] as $sku){
						$_product = $this->productRepository->get($sku);
						$htmljSon .= '"image": "'.$imageHelper->init($_product,'amp_category_page_grid')
                                            ->setImageFile($_product->getFile())
                                            ->resize($this->getStoreConfig('mgs_amp/catalog/product_image_width'),$this->getStoreConfig('mgs_amp/catalog/product_image_height'))
                                            ->getUrl().'"';
					}
				}
				
				$htmljSon .= '},';
				$html .= '</li>';
			}
			$html .= '</ul></amp-selector></div>';
		}
		$htmljSon = substr($htmljSon,0,-1);
		$htmljSon .= '}</script></amp-state>';
		return $htmljSon.$html;
	}
	
	public function getJsonTwoOption($data, $availableAttribute, $productId){
		$htmljSon = $html = '';
		$j=0; 
		$currencyHelper =  \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\Pricing\Helper\Data');
		foreach($data as $attributeId=>$option){
			$j++;
			if($j==1){
				$currentLabel = $availableAttribute[1];
				$nextLabel = $availableAttribute[2];
			}else{
				$currentLabel = $availableAttribute[2];
				$nextLabel = $availableAttribute[1];
			}
			
			$html .= '<div class="'.str_replace('_','',$option['attribute_code']).'-selector attribute-selector">';
			$html .= '<label for="'.str_replace('_','',$option['attribute_code']).'">'.$option['attribute_label'].': <span data-text="optionText'.$option['attribute_code'].$productId.'  ? optionText'.$option['attribute_code'].$productId.' : \'\'"></span></label>';
			$html .= '<amp-selector name="super_attribute['.$attributeId.']" layout="container" on="select:AMP.setState({'.str_replace('_','',$option['attribute_code']).$productId.': {selected'.ucfirst(str_replace('_','',$currentLabel)).': event.targetOption}})"><ul>';
				
			
			$htmljSon .= '<amp-state id="'.str_replace('_','',$option['attribute_code']).$productId.'"><script type="application/json">{';
			$i=0; foreach($option['option'] as $optionId=>$optionInfo){ 
				$optionVisual = $this->getAtributeSwatchHashcode($optionId);
				$bg = $img = false;
				if (strpos($optionVisual, '#') !== false) {
					$bg = true;
				}elseif (strpos($optionVisual, '/') !== false) {
					$img = true;
				}
			
				$html .= '<li option="'.$optionId.'" class="" [class]="(('.str_replace('_','',$nextLabel).$productId.'['.str_replace('_','',$nextLabel).$productId.'.selected'.ucfirst(str_replace('_','',$nextLabel)).'].'.ucfirst(str_replace('_','',$currentLabel)).'[\''.$optionId.'\'] == null) && '.str_replace('_','',$nextLabel).$productId.'.selected'.ucfirst(str_replace('_','',$nextLabel)).') ? \'unavailable\' : \'\'" on="tap:AMP.setState({optionText'.$option['attribute_code'].$productId.': \''.$optionInfo['label'].'\'})" role="option" tabindex="0">';
				
				
				if($bg){
					$html .= '<span class="option-background" style="background:'.$optionVisual.'" data-swatches="'.$optionVisual.'"></span>';
				}
				if($img){
					$html .= '<span class="option-image"><amp-img class="attribute-image" layout="responsive" src="'.$this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]).'attribute/swatch'.$optionVisual.'" width="28" height="28" alt=""></amp-img></span>';
				}
				
				if(!$bg && !$img){
					$html .= '<span class="option-text">'.$optionInfo['label'].'</span>';
				}
				
				$html .= '</li>';
			
				$i++;
				$htmljSon .= '"'.$optionId.'":{';
				$htmljSon .= '"id": "'.$i.'",';
				
				$htmljSon .= '"'.ucfirst(str_replace('_','',$nextLabel)).'": {';
				if(isset($optionInfo['product']) && count($optionInfo['product'])>0){
					foreach($optionInfo['product'] as $sku){
						$_product = $this->productRepository->get($sku);
						$htmljSon .= '"'.$_product->getData($nextLabel).'": "'.$currencyHelper->currency(number_format($_product->getPrice(),2),true,false).'",';
					}
				}
				$htmljSon = substr($htmljSon,0,-1);
				$htmljSon .= '}';
				
				$htmljSon .= '},';
			}
			$html .= '</ul></amp-selector>';
			$html .= '</div>';
			$htmljSon = substr($htmljSon,0,-1);
			$htmljSon .= '}</script></amp-state>';
		}
		return $htmljSon.$html;
	}
}