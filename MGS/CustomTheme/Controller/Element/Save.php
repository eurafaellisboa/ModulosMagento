<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
 
namespace MGS\CustomTheme\Controller\Element;

use Magento\Framework\Controller\ResultFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Cache\Manager as CacheManager;
class Save extends \MGS\Fbuilder\Controller\Element\Save
{
    public function execute()
    {
		if($this->customerSession->getUseFrontendBuilder() == 1){
			$data = $this->getRequest()->getPostValue();
			switch ($data['type']) {
				/* Static content Block */
				case "static":
					$this->removePanelImages('panel',$data);
					$content = $data['content'];
					
					$data['block_content'] = $content;
					$result['message'] = 'success';
					$sessionMessage = __('You saved the Text content block. Please wait for page reload.');
					break;
					
				case "owl_banner":
					$this->removePanelImages('slider',$data);
                    
                    if(isset($data['setting']['html_slider']) && $data['setting']['html_slider'] != ""){
                        $dataInit = ['autoplay', 'preload', 'stop_auto', 'navigation', 'pagination', 'loop', 'fullheight', 'rtl', 'hide_nav'];
                        
                        $data = $this->reInitData($data, $dataInit);
                        
                        $speed = '';
                        if($data['setting']['speed']){
                            $speed = $data['setting']['speed'];
                        }
                        
                        $sliderHtml = htmlentities($data['setting']['html_slider']);
                            
                        $dot = $data['setting']['pagination'];
                        
                        $content = '{{block class="MGS\Fbuilder\Block\Widget\OwlCarousel" autoplay="'.$data['setting']['autoplay'].'" stop_auto="'.$data['setting']['stop_auto'].'" navigation="'.$data['setting']['navigation'].'" hide_nav="'.$data['setting']['hide_nav'].'" nav_top="'.$data['setting']['nav_top'].'" navigation_position="'.$data['setting']['navigation_position'].'" pagination_position="'.$data['setting']['pagination_position'].'" fullheight="'.$data['setting']['fullheight'].'" preload="'.$data['setting']['preload'].'"height_preload="'.$data['setting']['height_preload'].'" pagination="'.$dot.'" loop="'.$data['setting']['loop'].'" rtl="'.$data['setting']['rtl'].'" speed="'.$speed.'" items="'.$data['setting']['items'].'" items_tablet="'.$data['setting']['items_tablet'].'" items_mobile="'.$data['setting']['items_mobile'].'" slide_margin="'.$data['setting']['slide_margin'].'" html_slider="'.$sliderHtml.'" template="widget/owl_slider.phtml"}}';
                        
                        $data['block_content'] = $content;
                        $result['message'] = 'success';
                        $sessionMessage = __('You saved the OWL Carousel Slider block. Please wait for page reload.');
                    }else{
						$result['message'] = __('You have not add any images to slider.');
					}
					break;
					
				/* New Products Block */
				case "new_products":
					$dataInit = ['use_slider', 'autoplay', 'stop_auto', 'rtl', 'navigation', 'loop', 'pagination', 'use_tabs', 'tab_ajax', 'hide_name', 'hide_review', 'hide_price', 'hide_addcart', 'hide_addwishlist', 'hide_addcompare', 'tab_font_bold', 'tab_italic', 'tab_uppercase', 'hide_nav'];
					$data = $this->reInitData($data, $dataInit);
					$categories = '';
					if(isset($data['setting']['category_id'])){
						$categories = implode(',',$data['setting']['category_id']);
					}
					if($data['setting']['template']=='list.phtml'){
						$data['setting']['use_tabs'] = 0;
					}
					if($data['setting']['use_tabs']){
						$template = 'products/category-tabs.phtml';
					}else{
						$template = 'products/'.$data['setting']['template'];
					}
					
					$content = '{{block class="MGS\Fbuilder\Block\Products\NewProducts" block_type="new" limit="'.$data['setting']['limit'].'" category_ids="'.$categories.'" use_slider="'.$data['setting']['use_slider'].'" hide_name="'.$data['setting']['hide_name'].'" hide_review="'.$data['setting']['hide_review'].'" hide_price="'.$data['setting']['hide_price'].'" hide_addcart="'.$data['setting']['hide_addcart'].'" hide_addwishlist="'.$data['setting']['hide_addwishlist'].'" hide_addcompare="'.$data['setting']['hide_addcompare'].'"';
					
					if($data['setting']['template']=='list.phtml'){
						$content .= ' numbercol="'.$data['setting']['numbercol'].'" percol="'.$data['setting']['percol'].'"';
					}
					
					if($data['setting']['template']=='grid.phtml'){
						$content .= ' perrow="'.$data['setting']['perrow'].'" perrow_tablet="'.$data['setting']['perrow_tablet'].'" perrow_mobile="'.$data['setting']['perrow_mobile'].'"';
					}
					
					if($data['setting']['use_slider']){
						$content .= ' autoplay="'.$data['setting']['autoplay'].'" stop_auto="'.$data['setting']['stop_auto'].'" navigation="'.$data['setting']['navigation'].'" hide_nav="'.$data['setting']['hide_nav'].'" nav_top="'.$data['setting']['nav_top'].'" navigation_position="'.$data['setting']['navigation_position'].'" pagination_position="'.$data['setting']['pagination_position'].'" pagination="'.$data['setting']['pagination'].'" number_row="'.$data['setting']['number_row'].'" slide_by="'.$data['setting']['slide_by'].'" loop="'.$data['setting']['loop'].'" rtl="'.$data['setting']['rtl'].'" slide_margin="'.$data['setting']['slide_margin'].'"';
					}
					
					if($data['setting']['use_tabs']){
						$content .= ' use_ajax="'.$data['setting']['tab_ajax'].'" tab_style="'.$data['setting']['tab_style'].'"';
						if($data['setting']['tab_font_bold']){
							$content .= ' tab_font_bold="1"';
						}
						if($data['setting']['tab_italic']){
							$content .= ' tab_italic="1"';
						}
						if($data['setting']['tab_uppercase']){
							$content .= ' tab_uppercase="1"';
						}
						$content .= ' tab_align="'.$data['setting']['tab_align'].'"';
						
						$data['custom_style_temp']['tab-style'] = ['tab-'.$data['setting']['tab_style']=>[
								'font-size' => $data['setting']['font_size'],
								'primary-color' => $data['setting']['tab_primary_color'],
								'secondary-color' => $data['setting']['tab_secondary_color'],
								'third-color' => $data['setting']['tab_third_color']
							]
						];
					}
					
					$content .= ' template="'.$template.'"}}';
					
					$data['block_content'] = $content;
					
					$result['message'] = 'success';
					$sessionMessage = __('You saved the New Products block. Please wait for page reload.');
					break;
					
				/* Attribute Products Block */
				case "attribute_products":
					$dataInit = ['use_slider', 'autoplay', 'stop_auto', 'rtl', 'navigation', 'loop', 'pagination', 'use_tabs', 'tab_ajax', 'hide_name', 'hide_review', 'hide_price', 'hide_addcart', 'hide_addwishlist', 'hide_addcompare', 'tab_font_bold', 'tab_italic', 'tab_uppercase', 'hide_nav'];
					$data = $this->reInitData($data, $dataInit);
					$categories = '';
					if(isset($data['setting']['category_id'])){
						$categories = implode(',',$data['setting']['category_id']);
					}
					if($data['setting']['template']=='list.phtml'){
						$data['setting']['use_tabs'] = 0;
					}
					if($data['setting']['use_tabs']){
						$template = 'products/category-tabs.phtml';
					}else{
						$template = 'products/'.$data['setting']['template'];
					}
					
					$content = '{{block class="MGS\Fbuilder\Block\Products\Attributes" block_type="attribute" attribute="'.$data['setting']['attribute'].'" limit="'.$data['setting']['limit'].'" category_ids="'.$categories.'" use_slider="'.$data['setting']['use_slider'].'" hide_name="'.$data['setting']['hide_name'].'" hide_review="'.$data['setting']['hide_review'].'" hide_price="'.$data['setting']['hide_price'].'" hide_addcart="'.$data['setting']['hide_addcart'].'" hide_addwishlist="'.$data['setting']['hide_addwishlist'].'" hide_addcompare="'.$data['setting']['hide_addcompare'].'"';
					
					if($data['setting']['template']=='list.phtml'){
						$content .= ' numbercol="'.$data['setting']['numbercol'].'" percol="'.$data['setting']['percol'].'"';
					}
					
					if($data['setting']['template']=='grid.phtml'){
						$content .= ' perrow="'.$data['setting']['perrow'].'" perrow_tablet="'.$data['setting']['perrow_tablet'].'" perrow_mobile="'.$data['setting']['perrow_mobile'].'"';
					}
					
					if($data['setting']['use_slider']){
						$content .= ' autoplay="'.$data['setting']['autoplay'].'" stop_auto="'.$data['setting']['stop_auto'].'" navigation="'.$data['setting']['navigation'].'" hide_nav="'.$data['setting']['hide_nav'].'" nav_top="'.$data['setting']['nav_top'].'" navigation_position="'.$data['setting']['navigation_position'].'" pagination_position="'.$data['setting']['pagination_position'].'" pagination="'.$data['setting']['pagination'].'" number_row="'.$data['setting']['number_row'].'" slide_by="'.$data['setting']['slide_by'].'" loop="'.$data['setting']['loop'].'" rtl="'.$data['setting']['rtl'].'" slide_margin="'.$data['setting']['slide_margin'].'"';
					}
					
					if($data['setting']['use_tabs']){
						$content .= ' use_ajax="'.$data['setting']['tab_ajax'].'" tab_style="'.$data['setting']['tab_style'].'"';
						if($data['setting']['tab_font_bold']){
							$content .= ' tab_font_bold="1"';
						}
						if($data['setting']['tab_italic']){
							$content .= ' tab_italic="1"';
						}
						if($data['setting']['tab_uppercase']){
							$content .= ' tab_uppercase="1"';
						}
						$content .= ' tab_align="'.$data['setting']['tab_align'].'"';
						
						$data['custom_style_temp']['tab-style'] = ['tab-'.$data['setting']['tab_style']=>[
								'font-size' => $data['setting']['font_size'],
								'primary-color' => $data['setting']['tab_primary_color'],
								'secondary-color' => $data['setting']['tab_secondary_color'],
								'third-color' => $data['setting']['tab_third_color']
							]
						];
					}
					
					$content .= ' template="'.$template.'"}}';
					
					$data['block_content'] = $content;
					
					$result['message'] = 'success';
					$sessionMessage = __('You saved the Products by Attribute block. Please wait for page reload.');
					break;
				
				/* Sale Products Block */
				case "sale":
					$dataInit = ['use_slider', 'autoplay', 'stop_auto', 'rtl', 'navigation', 'loop', 'pagination', 'use_tabs', 'tab_ajax', 'hide_name', 'hide_review', 'hide_price', 'hide_addcart', 'hide_addwishlist', 'hide_addcompare', 'tab_font_bold', 'tab_italic', 'tab_uppercase', 'hide_nav'];
					$data = $this->reInitData($data, $dataInit);
					$categories = '';
					if(isset($data['setting']['category_id'])){
						$categories = implode(',',$data['setting']['category_id']);
					}
					if($data['setting']['template']=='list.phtml'){
						$data['setting']['use_tabs'] = 0;
					}
					if($data['setting']['use_tabs']){
						$template = 'products/category-tabs.phtml';
					}else{
						$template = 'products/'.$data['setting']['template'];
					}
					
					$content = '{{block class="MGS\Fbuilder\Block\Products\Sale" block_type="sale" limit="'.$data['setting']['limit'].'" category_ids="'.$categories.'" use_slider="'.$data['setting']['use_slider'].'" hide_name="'.$data['setting']['hide_name'].'" hide_review="'.$data['setting']['hide_review'].'" hide_price="'.$data['setting']['hide_price'].'" hide_addcart="'.$data['setting']['hide_addcart'].'" hide_addwishlist="'.$data['setting']['hide_addwishlist'].'" hide_addcompare="'.$data['setting']['hide_addcompare'].'"';
					
					if($data['setting']['template']=='list.phtml'){
						$content .= ' numbercol="'.$data['setting']['numbercol'].'" percol="'.$data['setting']['percol'].'"';
					}
					
					if($data['setting']['template']=='grid.phtml'){
						$content .= ' perrow="'.$data['setting']['perrow'].'" perrow_tablet="'.$data['setting']['perrow_tablet'].'" perrow_mobile="'.$data['setting']['perrow_mobile'].'"';
					}
					
					if($data['setting']['use_slider']){
						$content .= ' autoplay="'.$data['setting']['autoplay'].'" stop_auto="'.$data['setting']['stop_auto'].'" navigation="'.$data['setting']['navigation'].'" hide_nav="'.$data['setting']['hide_nav'].'" nav_top="'.$data['setting']['nav_top'].'" navigation_position="'.$data['setting']['navigation_position'].'" pagination_position="'.$data['setting']['pagination_position'].'" pagination="'.$data['setting']['pagination'].'" number_row="'.$data['setting']['number_row'].'" slide_by="'.$data['setting']['slide_by'].'" loop="'.$data['setting']['loop'].'" rtl="'.$data['setting']['rtl'].'" slide_margin="'.$data['setting']['slide_margin'].'"';
					}
					
					if($data['setting']['use_tabs']){
						$content .= ' use_ajax="'.$data['setting']['tab_ajax'].'" tab_style="'.$data['setting']['tab_style'].'"';
						if($data['setting']['tab_font_bold']){
							$content .= ' tab_font_bold="1"';
						}
						if($data['setting']['tab_italic']){
							$content .= ' tab_italic="1"';
						}
						if($data['setting']['tab_uppercase']){
							$content .= ' tab_uppercase="1"';
						}
						$content .= ' tab_align="'.$data['setting']['tab_align'].'"';
						
						$data['custom_style_temp']['tab-style'] = ['tab-'.$data['setting']['tab_style']=>[
								'font-size' => $data['setting']['font_size'],
								'primary-color' => $data['setting']['tab_primary_color'],
								'secondary-color' => $data['setting']['tab_secondary_color'],
								'third-color' => $data['setting']['tab_third_color']
							]
						];
					}
					
					$content .= ' template="'.$template.'"}}';
					
					$data['block_content'] = $content;
					
					$result['message'] = 'success';
					$sessionMessage = __('You saved the Sale Products block. Please wait for page reload.');
					break;
				
				/* Top Rate Products Block */
				case "rate":
					$dataInit = ['use_slider', 'autoplay', 'stop_auto', 'rtl', 'navigation', 'loop', 'pagination', 'use_tabs', 'tab_ajax', 'hide_name', 'hide_review', 'hide_price', 'hide_addcart', 'hide_addwishlist', 'hide_addcompare', 'tab_font_bold', 'tab_italic', 'tab_uppercase', 'hide_nav'];
					$data = $this->reInitData($data, $dataInit);
					$categories = '';
					if(isset($data['setting']['category_id'])){
						$categories = implode(',',$data['setting']['category_id']);
					}
					if($data['setting']['template']=='list.phtml'){
						$data['setting']['use_tabs'] = 0;
					}
					if($data['setting']['use_tabs']){
						$template = 'products/category-tabs.phtml';
					}else{
						$template = 'products/'.$data['setting']['template'];
					}
					
					$content = '{{block class="MGS\Fbuilder\Block\Products\Rate" block_type="rate" limit="'.$data['setting']['limit'].'" category_ids="'.$categories.'" use_slider="'.$data['setting']['use_slider'].'" hide_name="'.$data['setting']['hide_name'].'" hide_review="'.$data['setting']['hide_review'].'" hide_price="'.$data['setting']['hide_price'].'" hide_addcart="'.$data['setting']['hide_addcart'].'" hide_addwishlist="'.$data['setting']['hide_addwishlist'].'" hide_addcompare="'.$data['setting']['hide_addcompare'].'"';
					
					if($data['setting']['template']=='list.phtml'){
						$content .= ' numbercol="'.$data['setting']['numbercol'].'" percol="'.$data['setting']['percol'].'"';
					}
					
					if($data['setting']['template']=='grid.phtml'){
						$content .= ' perrow="'.$data['setting']['perrow'].'" perrow_tablet="'.$data['setting']['perrow_tablet'].'" perrow_mobile="'.$data['setting']['perrow_mobile'].'"';
					}
					
					if($data['setting']['use_slider']){
						$content .= ' autoplay="'.$data['setting']['autoplay'].'" stop_auto="'.$data['setting']['stop_auto'].'" navigation="'.$data['setting']['navigation'].'" hide_nav="'.$data['setting']['hide_nav'].'" nav_top="'.$data['setting']['nav_top'].'" navigation_position="'.$data['setting']['navigation_position'].'" pagination_position="'.$data['setting']['pagination_position'].'" pagination="'.$data['setting']['pagination'].'" number_row="'.$data['setting']['number_row'].'" slide_by="'.$data['setting']['slide_by'].'" loop="'.$data['setting']['loop'].'" rtl="'.$data['setting']['rtl'].'" slide_margin="'.$data['setting']['slide_margin'].'"';
					}
					
					if($data['setting']['use_tabs']){
						$content .= ' use_ajax="'.$data['setting']['tab_ajax'].'" tab_style="'.$data['setting']['tab_style'].'"';
						if($data['setting']['tab_font_bold']){
							$content .= ' tab_font_bold="1"';
						}
						if($data['setting']['tab_italic']){
							$content .= ' tab_italic="1"';
						}
						if($data['setting']['tab_uppercase']){
							$content .= ' tab_uppercase="1"';
						}
						$content .= ' tab_align="'.$data['setting']['tab_align'].'"';
						
						$data['custom_style_temp']['tab-style'] = ['tab-'.$data['setting']['tab_style']=>[
								'font-size' => $data['setting']['font_size'],
								'primary-color' => $data['setting']['tab_primary_color'],
								'secondary-color' => $data['setting']['tab_secondary_color'],
								'third-color' => $data['setting']['tab_third_color']
							]
						];
					}
					
					$content .= ' template="'.$template.'"}}';
					
					$data['block_content'] = $content;
					
					$result['message'] = 'success';
					$sessionMessage = __('You saved the Top Rate Products block. Please wait for page reload.');
					break;
				
				/* Category Products Block */
				case "category_products":
					$dataInit = ['use_slider', 'cate_link', 'autoplay', 'stop_auto', 'rtl', 'navigation', 'loop', 'pagination', 'use_tabs', 'tab_ajax', 'hide_name', 'hide_review', 'hide_price', 'hide_addcart', 'hide_addwishlist', 'hide_addcompare', 'tab_font_bold', 'tab_italic', 'tab_uppercase', 'hide_nav'];
					$data = $this->reInitData($data, $dataInit);
					$categories = '';
					if(isset($data['setting']['category_id'])){
						$categories = implode(',',$data['setting']['category_id']);
					}
					if($data['setting']['template']=='list.phtml'){
						$data['setting']['use_tabs'] = 0;
					}
					if($data['setting']['use_tabs']){
						$template = 'products/category-tabs.phtml';
					}else{
						$template = 'products/'.$data['setting']['template'];
					}
					
					$content = '{{block class="MGS\Fbuilder\Block\Products\Category" block_type="catproduct" limit="'.$data['setting']['limit'].'" category_ids="'.$categories.'" use_slider="'.$data['setting']['use_slider'].'" cate_link="'.$data['setting']['cate_link'].'" hide_name="'.$data['setting']['hide_name'].'" hide_review="'.$data['setting']['hide_review'].'" hide_price="'.$data['setting']['hide_price'].'" hide_addcart="'.$data['setting']['hide_addcart'].'" mode="'.$data['setting']['template'].'" hide_addwishlist="'.$data['setting']['hide_addwishlist'].'" hide_addcompare="'.$data['setting']['hide_addcompare'].'"';
					
					if($data['setting']['template']=='list.phtml'){
						$content .= ' numbercol="'.$data['setting']['numbercol'].'" percol="'.$data['setting']['percol'].'"';
					}
					
					if($data['setting']['template']=='grid.phtml'){
						$content .= ' perrow="'.$data['setting']['perrow'].'" perrow_tablet="'.$data['setting']['perrow_tablet'].'" perrow_mobile="'.$data['setting']['perrow_mobile'].'"';
					}
                    
                    if($data['setting']['template']=='masonry.phtml'){
						$content .= ' perrow="'.$data['setting']['perrow'].'" perrow_tablet="'.$data['setting']['perrow_tablet'].'" perrow_mobile="'.$data['setting']['perrow_mobile'].'"';
					}
					
					if($data['setting']['use_slider']){
						$content .= ' autoplay="'.$data['setting']['autoplay'].'" stop_auto="'.$data['setting']['stop_auto'].'" navigation="'.$data['setting']['navigation'].'" hide_nav="'.$data['setting']['hide_nav'].'" nav_top="'.$data['setting']['nav_top'].'" navigation_position="'.$data['setting']['navigation_position'].'" pagination_position="'.$data['setting']['pagination_position'].'" pagination="'.$data['setting']['pagination'].'" number_row="'.$data['setting']['number_row'].'" slide_by="'.$data['setting']['slide_by'].'" loop="'.$data['setting']['loop'].'" rtl="'.$data['setting']['rtl'].'" slide_margin="'.$data['setting']['slide_margin'].'"';
					}
					
					if($data['setting']['use_tabs']){
						$content .= ' use_ajax="'.$data['setting']['tab_ajax'].'" tab_style="'.$data['setting']['tab_style'].'"';
						if($data['setting']['tab_font_bold']){
							$content .= ' tab_font_bold="1"';
						}
						if($data['setting']['tab_italic']){
							$content .= ' tab_italic="1"';
						}
						if($data['setting']['tab_uppercase']){
							$content .= ' tab_uppercase="1"';
						}
						$content .= ' tab_align="'.$data['setting']['tab_align'].'"';
						
						$data['custom_style_temp']['tab-style'] = ['tab-'.$data['setting']['tab_style']=>[
								'font-size' => $data['setting']['font_size'],
								'primary-color' => $data['setting']['tab_primary_color'],
								'secondary-color' => $data['setting']['tab_secondary_color'],
								'third-color' => $data['setting']['tab_third_color']
							]
						];
					}
					
					$content .= ' template="'.$template.'"}}';
					
					$data['block_content'] = $content;
					
					$result['message'] = 'success';
					$sessionMessage = __('You saved the Category Products block. Please wait for page reload.');
					break;
				
				/* Deals Block */
				case "deals":
					$dataInit = ['use_slider', 'autoplay', 'stop_auto', 'rtl', 'navigation', 'loop', 'pagination', 'use_tabs', 'hide_name', 'hide_review', 'hide_addcart', 'hide_addwishlist', 'hide_addcompare', 'tab_font_bold', 'tab_italic', 'tab_uppercase', 'hide_time', 'hide_saved_price', 'hide_discount', 'hide_nav'];
					$data = $this->reInitData($data, $dataInit);
					$categories = '';
					if(isset($data['setting']['category_id'])){
						$categories = implode(',',$data['setting']['category_id']);
					}
					if($data['setting']['template']=='list.phtml'){
						$data['setting']['use_tabs'] = 0;
					}
					if($data['setting']['use_tabs']){
						$template = 'products/deals/category-tabs.phtml';
					}else{
						$template = 'products/deals/'.$data['setting']['template'];
					}
					
					$content = '{{block class="MGS\Fbuilder\Block\Products\Deals" block_type="deals" limit="'.$data['setting']['limit'].'" category_ids="'.$categories.'" use_slider="'.$data['setting']['use_slider'].'" hide_name="'.$data['setting']['hide_name'].'" hide_review="'.$data['setting']['hide_review'].'" hide_addcart="'.$data['setting']['hide_addcart'].'" hide_addwishlist="'.$data['setting']['hide_addwishlist'].'" hide_addcompare="'.$data['setting']['hide_addcompare'].'" hide_time="'.$data['setting']['hide_time'].'" hide_discount="'.$data['setting']['hide_discount'].'" hide_saved_price="'.$data['setting']['hide_saved_price'].'"';
					
					if($data['setting']['template']=='list.phtml'){
						$content .= ' numbercol="'.$data['setting']['numbercol'].'" percol="'.$data['setting']['percol'].'"';
					}
					
					if($data['setting']['template']=='grid.phtml'){
						$content .= ' perrow="'.$data['setting']['perrow'].'" perrow_tablet="'.$data['setting']['perrow_tablet'].'" perrow_mobile="'.$data['setting']['perrow_mobile'].'"';
					}
					
					if($data['setting']['use_slider']){
						$content .= ' autoplay="'.$data['setting']['autoplay'].'" stop_auto="'.$data['setting']['stop_auto'].'" navigation="'.$data['setting']['navigation'].'" hide_nav="'.$data['setting']['hide_nav'].'" nav_top="'.$data['setting']['nav_top'].'" navigation_position="'.$data['setting']['navigation_position'].'" pagination_position="'.$data['setting']['pagination_position'].'" pagination="'.$data['setting']['pagination'].'" number_row="'.$data['setting']['number_row'].'" slide_by="'.$data['setting']['slide_by'].'" loop="'.$data['setting']['loop'].'" rtl="'.$data['setting']['rtl'].'" slide_margin="'.$data['setting']['slide_margin'].'"';
					}
					
					if($data['setting']['hide_discount'] != 1){
						$data['custom_style_temp']['discount-style'] = [
							'discount-color' => $data['setting']['discount_color'],
							'discount-background' => $data['setting']['discount_background'],
							'discount-width' => $data['setting']['discount_width'],
							'discount-font-size' => $data['setting']['discount_font_size']
						];
					}
					
					if($data['setting']['hide_time'] != 1){
						$content .= ' fbuilder_days="'.$this->encodeHtml($data['setting']['days']).'" fbuilder_hours="'.$this->encodeHtml($data['setting']['hours']).'" fbuilder_minutes="'.$this->encodeHtml($data['setting']['minutes']).'" fbuilder_seconds="'.$this->encodeHtml($data['setting']['seconds']).'"';
						
						$data['custom_style_temp']['deal-style'] = [
							'width' => $data['setting']['time_width'],
							'background-color' => $data['setting']['time_background'],
							'number-font-size' => $data['setting']['number_font_size'],
							'text-font-size' => $data['setting']['text_font_size'],
							'number-color' => $data['setting']['number_color'],
							'text-color' => $data['setting']['text_color']

						];
					}
					
					if($data['setting']['hide_saved_price'] != 1){
						$content .= ' fbuilder_saved_text="'.$this->encodeHtml($data['setting']['saved_text']).'"';
						
						$data['custom_style_temp']['saved-style'] = [
							'save-font-size' => $data['setting']['saved_font_size'],
							'saved-price-font-size' => $data['setting']['saved_price_font_size'],
							'saved-color' => $data['setting']['saved_color'],
							'saved-price-color' => $data['setting']['saved_price_color']

						];
					}
					
					
					if($data['setting']['use_tabs']){
						$content .= ' tab_style="'.$data['setting']['tab_style'].'"';
						if($data['setting']['tab_font_bold']){
							$content .= ' tab_font_bold="1"';
						}
						if($data['setting']['tab_italic']){
							$content .= ' tab_italic="1"';
						}
						if($data['setting']['tab_uppercase']){
							$content .= ' tab_uppercase="1"';
						}
						$content .= ' tab_align="'.$data['setting']['tab_align'].'"';
						
						$data['custom_style_temp']['tab-style'] = ['tab-'.$data['setting']['tab_style']=>[
								'font-size' => $data['setting']['font_size'],
								'primary-color' => $data['setting']['tab_primary_color'],
								'secondary-color' => $data['setting']['tab_secondary_color'],
								'third-color' => $data['setting']['tab_third_color']
							]
						];
					}
					
					$content .= ' template="'.$template.'"}}';
					
					$data['block_content'] = $content;
					
					$result['message'] = 'success';
					$sessionMessage = __('You saved the Deals block. Please wait for page reload.');
					break;
				
				/* Single Product Block */
				case "special_deal":
					$dataInit = ['hide_name', 'hide_review', 'hide_price', 'hide_addcart', 'hide_addwishlist', 'hide_addcompare', 'hide_description'];
					$data = $this->reInitData($data, $dataInit);
					
					$content = '{{block class="MGS\Fbuilder\Block\Products\SpecialProduct" sub_title="'.$data['setting']['sub_title'].'" product_id="'.$data['setting']['product_id'].'" style-template="'.$data['setting']['style-template'].'" hide_name="'.$data['setting']['hide_name'].'" hide_review="'.$data['setting']['hide_review'].'" hide_price="'.$data['setting']['hide_price'].'" hide_addcart="'.$data['setting']['hide_addcart'].'" hide_description="'.$data['setting']['hide_description'].'" truncate="'.$data['setting']['truncate'].'"';
					
					$content .= ' fbuilder_days="'.$this->encodeHtml($data['setting']['days']).'" fbuilder_hours="'.$this->encodeHtml($data['setting']['hours']).'" fbuilder_minutes="'.$this->encodeHtml($data['setting']['minutes']).'" fbuilder_seconds="'.$this->encodeHtml($data['setting']['seconds']).'"';
					
					$content .= ' template="products/single/deals.phtml"}}';

					$data['block_content'] = $content;
					$result['message'] = 'success';
					$sessionMessage = __('You saved the Single Product block. Please wait for page reload.');
					break;
					
				/* Product Tabs Block */
				case "tabs":
					if(isset($data['setting']['tabs']) && count($data['setting']['tabs'])>0){
						$tabs = $data['setting']['tabs'];

						$dataInit = ['use_slider', 'autoplay', 'stop_auto', 'rtl', 'navigation', 'loop', 'pagination', 'hide_name', 'hide_review', 'hide_price', 'hide_addcart', 'hide_addwishlist', 'hide_addcompare', 'tab_ajax', 'tab_font_bold', 'tab_italic', 'tab_uppercase', 'hide_nav'];
						$data = $this->reInitData($data, $dataInit);
						$categories = '';
						if(isset($data['setting']['category_id'])){
							$categories = implode(',',$data['setting']['category_id']);
						}
						
						usort($tabs, function ($item1, $item2) {
							if ($item1['position'] == $item2['position']) return 0;
							return $item1['position'] < $item2['position'] ? -1 : 1;
						});
						
						$tabType = $tabLabel = [];
						foreach($tabs as $tab){
							$tabType[] = $tab['value'];
							$tabLabel[] = $tab['label'];
						}
						$tabs = implode(',',$tabType);
						$labels = implode(',',$tabLabel);
						
						$content = '{{block class="MGS\Fbuilder\Block\Products\Tabs" limit="'.$data['setting']['limit'].'" category_ids="'.$categories.'" use_slider="'.$data['setting']['use_slider'].'" hide_name="'.$data['setting']['hide_name'].'" hide_review="'.$data['setting']['hide_review'].'" hide_price="'.$data['setting']['hide_price'].'" hide_addcart="'.$data['setting']['hide_addcart'].'" hide_addwishlist="'.$data['setting']['hide_addwishlist'].'" hide_addcompare="'.$data['setting']['hide_addcompare'].'" tabs="'.$tabs.'" perrow="'.$data['setting']['perrow'].'" perrow_tablet="'.$data['setting']['perrow_tablet'].'" perrow_mobile="'.$data['setting']['perrow_mobile'].'"';
						
						if($data['setting']['use_slider']){
							$content .= ' autoplay="'.$data['setting']['autoplay'].'" stop_auto="'.$data['setting']['stop_auto'].'" navigation="'.$data['setting']['navigation'].'" hide_nav="'.$data['setting']['hide_nav'].'" nav_top="'.$data['setting']['nav_top'].'" navigation_position="'.$data['setting']['navigation_position'].'" pagination_position="'.$data['setting']['pagination_position'].'" pagination="'.$data['setting']['pagination'].'" number_row="'.$data['setting']['number_row'].'" slide_by="'.$data['setting']['slide_by'].'" loop="'.$data['setting']['loop'].'" rtl="'.$data['setting']['rtl'].'" slide_margin="'.$data['setting']['slide_margin'].'"';
						}
						
						$content .=' labels="'.$labels.'"';
						
						$content .= ' use_ajax="'.$data['setting']['tab_ajax'].'" tab_style="'.$data['setting']['tab_style'].'"';
						if($data['setting']['tab_font_bold']){
							$content .= ' tab_font_bold="1"';
						}
						if($data['setting']['tab_italic']){
							$content .= ' tab_italic="1"';
						}
						if($data['setting']['tab_uppercase']){
							$content .= ' tab_uppercase="1"';
						}
						$content .= ' tab_align="'.$data['setting']['tab_align'].'"';
						
						$data['custom_style_temp']['tab-style'] = ['tab-'.$data['setting']['tab_style']=>[
								'font-size' => $data['setting']['font_size'],
								'primary-color' => $data['setting']['tab_primary_color'],
								'secondary-color' => $data['setting']['tab_secondary_color'],
								'third-color' => $data['setting']['tab_third_color']
							]
						];
						
						$content .= ' template="products/tabs/view.phtml"}}';
						
						$data['block_content'] = $content;
						$result['message'] = 'success';
						$sessionMessage = __('You saved the Product Tabs block. Please wait for page reload.');
					}else{
						$result['message'] = __('You have not add any tabs.');
					}
					break;
				
				/* Single Product Block */
				case "special_product":
					$dataInit = ['hide_name', 'hide_review', 'hide_price', 'hide_addcart', 'hide_addwishlist', 'hide_addcompare', 'hide_description'];
					$data = $this->reInitData($data, $dataInit);
					
					$content = '{{block class="MGS\Fbuilder\Block\Products\SpecialProduct" product_id="'.$data['setting']['product_id'].'" hide_name="'.$data['setting']['hide_name'].'" hide_review="'.$data['setting']['hide_review'].'" hide_price="'.$data['setting']['hide_price'].'" hide_addcart="'.$data['setting']['hide_addcart'].'" hide_addwishlist="'.$data['setting']['hide_addwishlist'].'" hide_addcompare="'.$data['setting']['hide_addcompare'].'" hide_description="'.$data['setting']['hide_description'].'" truncate="'.$data['setting']['truncate'].'"';
					
					$content .= ' template="products/single/default.phtml"}}';

					$data['block_content'] = $content;
					$result['message'] = 'success';
					$sessionMessage = __('You saved the Single Product block. Please wait for page reload.');
					break;
				/* Facebook Fan Box Block */
				case "facebook":
					$dataInit = ['hide_cover', 'show_facepile', 'hide_call_to', 'small_header', 'fit_inside'];
					$data = $this->reInitData($data, $dataInit);
					$tabs = implode(',',$data['setting']['facebook_tabs']);
					$content = '{{block class="MGS\Fbuilder\Block\Social\Facebook" page_url="'.$data['setting']['page_url'].'" width="'.$data['setting']['width'].'" height="'.$data['setting']['height'].'" facebook_tabs="'.$tabs.'" hide_cover="'.$data['setting']['hide_cover'].'" show_facepile="'.$data['setting']['show_facepile'].'" small_header="'.$data['setting']['small_header'].'" fit_inside="'.$data['setting']['fit_inside'].'" hide_call_to="'.$data['setting']['hide_call_to'].'" template="widget/socials/facebook_fanbox.phtml"}}';
					
					$data['block_content'] = $content;
					$result['message'] = 'success';
					$sessionMessage = __('You saved the Facebook Fanbox block. Please wait to reload page.');
					break;
					
				/* Twitter Fan Box Block */
				case "twitter":
					$content = '{{block class="MGS\Fbuilder\Block\Social\Twitter" page_url="'.$data['setting']['page_url'].'" width="'.$data['setting']['width'].'" height="'.$data['setting']['height'].'" theme="'.$data['setting']['theme'].'" default_link_color="'.$data['setting']['default_link_color'].'" language="'.$data['setting']['language'].'" template="widget/socials/twitter_timeline.phtml"}}';
					
					$data['block_content'] = $content;
					$result['message'] = 'success';
					$sessionMessage = __('You saved the Twitter Timeline block. Please wait to reload page.');
					break;
					
				/* Instagram Block */
				case "instagram":
					$dataInit = ['link', 'like', 'use_slider', 'autoplay', 'stop_auto', 'rtl', 'loop', 'navigation', 'pagination', 'comment', 'hide_nav'];
					$data = $this->reInitData($data, $dataInit);
					$content = '{{block class="MGS\Fbuilder\Block\Social\Instagram" limit="'.$data['setting']['limit'].'" resolution="'.$data['setting']['resolution'].'" link="'.$data['setting']['link'].'" perrow="'.$data['setting']['perrow'].'" perrow_tablet="'.$data['setting']['perrow_tablet'].'" perrow_mobile="'.$data['setting']['perrow_mobile'].'" use_slider="'.$data['setting']['use_slider'].'" like="'.$data['setting']['like'].'" comment="'.$data['setting']['comment'].'"';
					
					if($data['setting']['use_slider']){
						$content .= ' autoplay="'.$data['setting']['autoplay'].'" stop_auto="'.$data['setting']['stop_auto'].'" navigation="'.$data['setting']['navigation'].'" hide_nav="'.$data['setting']['hide_nav'].'" nav_top="'.$data['setting']['nav_top'].'" navigation_position="'.$data['setting']['navigation_position'].'" pagination_position="'.$data['setting']['pagination_position'].'" pagination="'.$data['setting']['pagination'].'" number_row="'.$data['setting']['number_row'].'" slide_by="'.$data['setting']['slide_by'].'" rtl="'.$data['setting']['rtl'].'" slide_margin="'.$data['setting']['slide_margin'].'" loop="'.$data['setting']['loop'].'"';
					}
					
					$content .=  ' template="widget/socials/instagram.phtml"}}';
					
					$data['block_content'] = $content;
					$result['message'] = 'success';
					$sessionMessage = __('You saved the Instagram block. Please wait to reload page.');
					break;
					
				/* Snapppt Block*/
				case "instagram_shop":
					$content = '{{block class="MGS\Fbuilder\Block\Social\Snapppt" template="widget/socials/snapppt.phtml"}}';
					
					$data['block_content'] = $content;
					$result['message'] = 'success';
					$sessionMessage = __('You saved the Instagram Shop block. Please wait to reload page.');
					break;
					
				/* Category List */	
				case "category_list":
					$dataInit = ['use_slider', 'autoplay', 'stop_auto', 'rtl', 'navigation', 'loop', 'pagination', 'show_category_name', 'show_product', 'show_icon', 'font_bold', 'font_italic', 'uppercase', 'other_font_bold', 'other_font_italic', 'other_uppercase', 'hide_nav'];
					$data = $this->reInitData($data, $dataInit);
					
					$categories = '';
					if(isset($data['setting']['category_id'])){
						$categories = implode(',',$data['setting']['category_id']);
					}
					
					$content = '{{block class="MGS\Fbuilder\Block\Widget\CategoryList" fbuilder_title="'.$this->encodeHtml($data['setting']['title']).'" category_ids="'.$categories.'"';
					
					if($data['setting']['template']=='grid.phtml'){
						$content .= ' use_slider="'.$data['setting']['use_slider'].'" perrow="'.$data['setting']['perrow'].'" perrow_tablet="'.$data['setting']['perrow_tablet'].'" perrow_mobile="'.$data['setting']['perrow_mobile'].'" show_category_name="'.$data['setting']['show_category_name'].'" show_product="'.$data['setting']['show_product'].'"';
						
						if($data['setting']['use_slider']){
							$content .= ' autoplay="'.$data['setting']['autoplay'].'" stop_auto="'.$data['setting']['stop_auto'].'" navigation="'.$data['setting']['navigation'].'" hide_nav="'.$data['setting']['hide_nav'].'" nav_top="'.$data['setting']['nav_top'].'" navigation_position="'.$data['setting']['navigation_position'].'" pagination_position="'.$data['setting']['pagination_position'].'" pagination="'.$data['setting']['pagination'].'" number_row="'.$data['setting']['number_row'].'" slide_by="'.$data['setting']['slide_by'].'" rtl="'.$data['setting']['rtl'].'" slide_margin="'.$data['setting']['slide_margin'].'" loop="'.$data['setting']['loop'].'"';
						}
						
						$data['custom_style_temp']['category-style'] = ['grid'=>[
							'font-size' => $data['setting']['font_size'],
							'other-font-size' => $data['setting']['other_font_size'],
							'primary-color' => $data['setting']['primary_color'],
							'secondary-color' => $data['setting']['secondary_color'],
							'third-color' => $data['setting']['third_color']
						]];
						
					}else{
						$content .= ' show_icon="'.$data['setting']['show_icon'].'"';
						
						$data['custom_style_temp']['category-style'] = ['list'=>[
							'font-size' => $data['setting']['font_size'],
							'other-font-size' => $data['setting']['other_font_size'],
							'primary-color' => $data['setting']['primary_color'],
							'secondary-color' => $data['setting']['secondary_color'],
							'third-color' => $data['setting']['third_color'],
							'fourth-color' => $data['setting']['fourth_color'],
							'fifth_color' => $data['setting']['fifth_color'],
						]];
					}
					
					if($data['setting']['font_bold']){
						$content .= ' font_bold="1"';
					}
					if($data['setting']['font_italic']){
						$content .= ' font_italic="1"';
					}
					if($data['setting']['uppercase']){
						$content .= ' uppercase="1"';
					}
					
					if($data['setting']['other_font_bold']){
						$content .= ' other_font_bold="1"';
					}
					if($data['setting']['other_font_italic']){
						$content .= ' other_font_italic="1"';
					}
					if($data['setting']['other_uppercase']){
						$content .= ' other_uppercase="1"';
					}
					
					$content .=  ' template="widget/category/'.$data['setting']['template'].'"}}';
					
					$data['block_content'] = $content;
					$result['message'] = 'success';
					$sessionMessage = __('You saved the Category list block. Please wait to reload page.');
					break;
				
				/* Accordion Block*/
				case "accordion":
					if(isset($data['setting']['accordion']) && count($data['setting']['accordion'])>0){
						$accordions = $data['setting']['accordion'];

						$dataInit = ['collapse_all', 'title_font_bold', 'title_font_italic', 'title_uppercase', 'active_font_bold', 'active_font_italic', 'active_uppercase'];
						$data = $this->reInitData($data, $dataInit);
						
						usort($accordions, function ($item1, $item2) {
							if ($item1['position'] == $item2['position']) return 0;
							return $item1['position'] < $item2['position'] ? -1 : 1;
						});
						
						$accordionContent = $accordionLabel = [];
						foreach($accordions as $accordion){
							$accordionContent[] = $this->encodeHtml($accordion['content']);
							$accordionLabel[] = $this->encodeHtml($accordion['label']);
						}
						
						$accordionData = implode(',',$accordionContent);
						$labels = implode(',',$accordionLabel);
						
						$content = '{{block class="Magento\Framework\View\Element\Template" accordion_content="'.$accordionData.'" accordion_label="'.$labels.'" collapse_all="'.$data['setting']['collapse_all'].'" accordion_icon="'.$data['setting']['accordion_icon'].'" icon_position="'.$data['setting']['icon_position'].'"';
						
						$content .= ' template="MGS_Fbuilder::widget/accordion.phtml"}}';
						
						$data['custom_style_temp']['accordion-style'] = [
							'margin' => $data['setting']['accordion_margin'],
							'padding' => $data['setting']['accordion_padding'],
							'font-size' => $data['setting']['title_font_size'],
							'height' => $data['setting']['title_height'],
							'title-color' => $data['setting']['title_font_color'],
							'title-background' => $data['setting']['title_background'],
							'title-bold' => $data['setting']['title_font_bold'],
							'title-italic' => $data['setting']['title_font_italic'],
							'title-uppercase' => $data['setting']['title_uppercase'],
							'active-color' => $data['setting']['active_font_color'],
							'active-background' => $data['setting']['active_background'],
							'active-bold' => $data['setting']['active_font_bold'],
							'active-italic' => $data['setting']['active_font_italic'],
							'active-uppercase' => $data['setting']['active_uppercase'],
							'icon-color' => $data['setting']['icon_font_color'],
							'icon-background' => $data['setting']['icon_background'],
							'icon-size' => $data['setting']['icon_font_size'],
							'icon-position' => $data['setting']['icon_position'],
							'icon-type' => $data['setting']['accordion_icon'],
						];
						
						$data['block_content'] = $content;
						$result['message'] = 'success';
						$sessionMessage = __('You saved the Accordion block. Please wait for page reload.');
					}else{
						$result['message'] = __('You have not add any content for Accordion.');
					}
					break;
				
				/* Video Block*/
				case "video":
					
					$dataInit = ['full_width','autoplay','hide_info','hide_control','loop','mute'];
					$data = $this->reInitData($data, $dataInit);
					
					$content = '{{block class="MGS\Fbuilder\Block\Widget\Video" video_url="'.$data['setting']['video_url'].'" full_width="'.$data['setting']['full_width'].'" video_width="'.$data['setting']['video_width'].'" video_height="'.$data['setting']['video_height'].'" autoplay="'.$data['setting']['autoplay'].'" hide_info="'.$data['setting']['hide_info'].'" hide_control="'.$data['setting']['hide_control'].'" loop="'.$data['setting']['loop'].'" mute="'.$data['setting']['mute'].'"';
					
					$content .= ' template="widget/video.phtml"}}';
					
					
					$data['block_content'] = $content;
					$result['message'] = 'success';
					$sessionMessage = __('You saved the Video block. Please wait for page reload.');
					
					break;
				
				/* Google Map Block*/
				case "map":
					$dataInit = ['location_address','location','address_box','wheel','navigation','type_control','scale','draggable','grayscale'];
					$data = $this->reInitData($data, $dataInit);
					
					if(isset($data['remove_pin_image']) && ($data['remove_pin_image']==1)){
						$filePath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('mgs/fbuilder/map/') . $data['setting']['map_icon'];
						if ($this->_file->isExists($filePath))  {
							$this->_file->deleteFile($filePath);
						}
						
						$data['setting']['map_icon'] = '';
					}
					
					if(isset($_FILES['pin_icon']) && $_FILES['pin_icon']['name'] != '') {
						try {
							
							if(isset($data['setting']['map_icon']) && ($data['setting']['map_icon']!='')){
								$filePath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('mgs/fbuilder/map') . $data['setting']['map_icon'];
								if ($this->_file->isExists($filePath))  {
									$this->_file->deleteFile($filePath);
								}
							}
							
							$uploader = $this->uploadFile('pin_icon', 'map');
							$data['setting']['map_icon'] = $uploader->getUploadedFileName();
						} catch (\Exception $e) {
							$result['message'] = $e->getMessage();
						}
					}
					
					$content = '{{block class="MGS\Fbuilder\Block\Widget\Map" location_address="'.$data['setting']['location_address'].'" location="'.$data['setting']['location'].'" address_box="'.$data['setting']['address_box'].'" map_height="'.$data['setting']['map_height'].'" map_zoom="'.$data['setting']['map_zoom'].'" wheel="'.$data['setting']['wheel'].'" navigation="'.$data['setting']['navigation'].'" type_control="'.$data['setting']['type_control'].'" scale="'.$data['setting']['scale'].'" draggable="'.$data['setting']['draggable'].'" grayscale="'.$data['setting']['grayscale'].'" lat="'.$data['setting']['lat'].'" long="'.$data['setting']['long'].'" fbuilder_address="'.$this->encodeHtml($data['setting']['address']).'" map_icon="'.$data['setting']['map_icon'].'"';
					
					if($data['setting']['address_box']){
						$content .= ' fbuilder_address_title="'.$this->encodeHtml($data['setting']['address_title']).'" fbuilder_line_one="'.$this->encodeHtml($data['setting']['line_one']).'" fbuilder_line_two="'.$this->encodeHtml($data['setting']['line_two']).'" fbuilder_line_three="'.$this->encodeHtml($data['setting']['line_three']).'" fbuilder_line_four="'.$this->encodeHtml($data['setting']['line_four']).'" fbuilder_line_five="'.$this->encodeHtml($data['setting']['line_five']).'"';
						
						$data['custom_style_temp']['map-style'] = [
							'background' => $data['setting']['box_background'],
							'color' => $data['setting']['box_color'],
							'width' => $data['setting']['box_width'],
							'size' => $data['setting']['font_size'],
							'title-size' => $data['setting']['title_font_size']
						];
					}
					
					$content .= ' template="widget/map.phtml"}}';
					
					
					$data['block_content'] = $content;
					$result['message'] = 'success';
					$sessionMessage = __('You saved the Google Map block. Please wait for page reload.');
					
					break;
					
				/* Promo Banner Block*/
				case "promo_banner":
					$dataInit = [];
					$data = $this->reInitData($data, $dataInit);
					
					if(isset($_FILES['image']) && $_FILES['image']['name'] != '') {
						try {
							/* if(isset($data['setting']['banner_image']) && ($data['setting']['banner_image']!='')){
								$filePath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('mgs/fbuilder/promobanners') . $data['setting']['banner_image'];
								if ($this->_file->isExists($filePath))  {
									$this->_file->deleteFile($filePath);
								}
							} */
							
							$uploader = $this->uploadFile('image', 'promobanners');
							$data['setting']['banner_image'] = $uploader->getUploadedFileName();
							
							
							
						} catch (\Exception $e) {
							$result['message'] = $e->getMessage();
						}
					}
					
					$content = '{{block class="MGS\Fbuilder\Block\Widget\PromoBanner" banner_image="'.$data['setting']['banner_image'].'" url="'.$data['setting']['url'].'" fbuilder_text_content="'.$this->encodeHtml($data['setting']['text_content']).'" fbuilder_button_text="'.$this->encodeHtml($data['setting']['button_text']).'" text_align="'.$data['setting']['text_align'].'" effect="'.$data['setting']['effect'].'"';
					
					$content .= ' template="widget/promobanner.phtml"}}';
					
					$data['custom_style_temp']['banner-style'] = [
						'text-color' => $data['setting']['text_color'],
						'button-background' => $data['setting']['button_background'],
						'button-color' => $data['setting']['button_color'],
						'button-border' => $data['setting']['button_border'],
						'button-hover-background' => $data['setting']['button_hover_background'],
						'button-hover-color' => $data['setting']['button_hover_color'],
						'button-hover-border' => $data['setting']['button_hover_border']
					];
					
					
					$data['block_content'] = $content;
					$result['message'] = 'success';
					$sessionMessage = __('You saved the Promo Banner block. Please wait for page reload.');
					
					break;
					
				/* Profile Block*/
				case "profile":
					$dataInit = ['social_box_shadow'];
					$data = $this->reInitData($data, $dataInit);
					
					if(isset($_FILES['photo']) && $_FILES['photo']['name'] != '') {
						try {
							
							/* if(isset($data['setting']['profile_photo']) && ($data['setting']['profile_photo']!='')){
								$filePath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('mgs/fbuilder/profiles') . $data['setting']['profile_photo'];
								if ($this->_file->isExists($filePath))  {
									$this->_file->deleteFile($filePath);
								}
							} */
							
							$uploader = $this->uploadFile('photo', 'profiles');
							$data['setting']['profile_photo'] = $uploader->getUploadedFileName();
						} catch (\Exception $e) {
							$result['message'] = $e->getMessage();
						}
					}
					
					$content = '{{block class="MGS\Fbuilder\Block\Widget\Profile" profile_photo="'.$data['setting']['profile_photo'].'" fbuilder_profile_name="'.$this->encodeHtml($data['setting']['profile_name']).'" fbuilder_subtitle="'.$this->encodeHtml($data['setting']['subtitle']).'" fbuilder_text_content="'.$this->encodeHtml($data['setting']['text_content']).'" email="'.$data['setting']['email'].'" facebook="'.$data['setting']['facebook'].'" twitter="'.$data['setting']['twitter'].'" linkedin="'.$data['setting']['linkedin'].'" social_box_shadow="'.$data['setting']['social_box_shadow'].'"'; 
					
					$content .= ' template="widget/profile/'.$data['setting']['style'].'.phtml"}}';
					
					$data['custom_style_temp']['profile-style'] = [
						'name-font-size' => $data['setting']['name_font_size'],
						'name-font-color' => $data['setting']['name_font_color'],
						'subtitle-font-size' => $data['setting']['subtitle_font_size'],
						'subtitle-font-color' => $data['setting']['subtitle_font_color'],
						'subtitle-border-color' => $data['setting']['subtitle_border_color'],
						'desc-font-size' => $data['setting']['desc_font_size'],
						'desc-font-color' => $data['setting']['desc_font_color'],
						'social-font-size' => $data['setting']['social_font_size'],
						'social-box-width' => $data['setting']['social_box_width'],
						'social-font-color' => $data['setting']['social_font_color'],
						'social-background' => $data['setting']['social_background'],
						'social-border' => $data['setting']['social_border'],
						'social-hover-color' => $data['setting']['social_hover_color'],
						'social-hover-background' => $data['setting']['social_hover_background'],
						'social-hover-border' => $data['setting']['social_hover_border'],
						'social-box-shadow' => $data['setting']['social_box_shadow']
					];
					
					
					$data['block_content'] = $content;
					$result['message'] = 'success';
					$sessionMessage = __('You saved the Profile block. Please wait for page reload.');
					
					break;
					
				/* Content Block*/
				case "content_box":
					$content = '{{block class="Magento\Framework\View\Element\Template" fbuilder_icon="'.$this->encodeHtml($data['setting']['icon']).'" fbuilder_subtitle="'.$this->encodeHtml($data['setting']['subtitle']).'" fbuilder_text_content="'.$this->encodeHtml($data['setting']['text_content']).'" style="'.$data['setting']['style'].'" link="'.$this->encodeHtml($data['setting']['link']).'"'; 
					
					$content .= ' template="MGS_Fbuilder::widget/content_box.phtml"}}';
					
					$data['custom_style_temp']['box-style'] = [
						'icon_font_size' => $data['setting']['icon_font_size'],
						'border' => $data['setting']['border'],
						'border_hover' => $data['setting']['border_hover'],
						'border_width' => $data['setting']['border_width'],
						'width' => $data['setting']['width'],
						'icon_color' => $data['setting']['icon_color'],
						'icon_color_hover' => $data['setting']['icon_color_hover'],
						'icon_background' => $data['setting']['icon_background'],
						'icon_background_hover' => $data['setting']['icon_background_hover'],
						'subtitle_font_size' => $data['setting']['subtitle_font_size'],
						'subtitle_font_color' => $data['setting']['subtitle_font_color'],
						'subtitle_color_hover' => $data['setting']['subtitle_color_hover'],
						'desc_font_size' => $data['setting']['desc_font_size'],
						'style' => $data['setting']['style']
					];
					
					
					$data['block_content'] = $content;
					$result['message'] = 'success';
					$sessionMessage = __('You saved the Content Box block. Please wait for page reload.');
					
					break;
				
				/* Counter Block*/
				case "counter_box":
					$content = '{{block class="Magento\Framework\View\Element\Template" fbuilder_icon="'.$this->encodeHtml($data['setting']['icon']).'" fbuilder_subtitle="'.$this->encodeHtml($data['setting']['subtitle']).'" fbuilder_text_content="'.$this->encodeHtml($data['setting']['text_content']).'" style="'.$data['setting']['style'].'" icon_font_size="'.$data['setting']['icon_font_size'].'" border="'.$data['setting']['border'].'" border_width="'.$data['setting']['border_width'].'" width="'.$data['setting']['width'].'" icon_color="'.$data['setting']['icon_color'].'" icon_color="'.$data['setting']['icon_color'].'" icon_background="'.$data['setting']['icon_background'].'" subtitle_font_size="'.$data['setting']['subtitle_font_size'].'" subtitle_font_color="'.$data['setting']['subtitle_font_color'].'" desc_font_size="'.$data['setting']['desc_font_size'].'" box_border="'.$data['setting']['box_border'].'" number_color="'.$data['setting']['number_color'].'" number_font_size="'.$data['setting']['number_font_size'].'" number_from="'.$data['setting']['number_from'].'" number_to="'.$data['setting']['number_to'].'" duration="'.$data['setting']['duration'].'" separators="'.$data['setting']['separators'].'" template="MGS_Fbuilder::widget/counter_box.phtml"}}';
					
					$data['block_content'] = $content;
					$result['message'] = 'success';
					$sessionMessage = __('You saved the Counter Box block. Please wait for page reload.');
					
					break;
				
				/* Countdown Box*/
				case "countdown_box":
					$dataInit = ['date_fontweight','text_fontweight'];
					$data = $this->reInitData($data, $dataInit);
					$content = '{{block class="Magento\Framework\View\Element\Template" fbuilder_days="'.$this->encodeHtml($data['setting']['days']).'" fbuilder_coundown_date="'.$this->encodeHtml($data['setting']['coundown_date']).'" fbuilder_hours="'.$this->encodeHtml($data['setting']['hours']).'" fbuilder_minutes="'.$this->encodeHtml($data['setting']['minutes']).'" fbuilder_seconds="'.$this->encodeHtml($data['setting']['seconds']).'" date_fontweight="'.$data['setting']['date_fontweight'].'" text_fontweight="'.$data['setting']['text_fontweight'].'" position="'.$data['setting']['position'].'" template="MGS_Fbuilder::widget/countdown_box.phtml"}}'; 
					
					$data['custom_style_temp']['countdown-style'] = [
						'date_font_size' => $data['setting']['date_font_size'],
						'date_fontweight' => $data['setting']['date_fontweight'],
						'date_color' => $data['setting']['date_color'],
						'date_background' => $data['setting']['date_background'],
						'date_border' => $data['setting']['date_border'],
						'date_border_size' => $data['setting']['date_border_size'],
						'date_border_radius' => $data['setting']['date_border_radius'],
						'text_font_size' => $data['setting']['text_font_size'],
						'text_color' => $data['setting']['text_color'],
						'position' => $data['setting']['position']
					];
					
					$data['block_content'] = $content;
					$result['message'] = 'success';
					$sessionMessage = __('You saved the Countdown Box block. Please wait for page reload.');
					
					break;
				
				/* Progress bar Block*/
				case "progress_bar":
					$dataInit = ['box_animation','percent_fontweight','title_fontweight'];
					$data = $this->reInitData($data, $dataInit);
					$content = '{{block class="Magento\Framework\View\Element\Template" fbuilder_subtitle="'.$this->encodeHtml($data['setting']['subtitle']).'" percent="'.$data['setting']['percent'].'" percent_font_size="'.$data['setting']['percent_font_size'].'" percent_color="'.$data['setting']['percent_color'].'" percent_background="'.$data['setting']['percent_background'].'" percent_fontweight="'.$data['setting']['percent_fontweight'].'" title_font_size="'.$data['setting']['title_font_size'].'" title_color="'.$data['setting']['title_color'].'" title_fontweight="'.$data['setting']['title_fontweight'].'" bar_background="'.$data['setting']['bar_background'].'" progress_background="'.$data['setting']['progress_background'].'" bar_height="'.$data['setting']['bar_height'].'" border_radius="'.$data['setting']['border_radius'].'" box_shadow="'.$data['setting']['box_shadow'].'" box_animation="'.$data['setting']['box_animation'].'" position="'.$data['setting']['position'].'" template="MGS_Fbuilder::widget/progress_bar.phtml"}}'; 
					
					$data['block_content'] = $content;
					$result['message'] = 'success';
					$sessionMessage = __('You saved the Progress Bar block. Please wait for page reload.');
					
					break;
					
				/* Progress circle Block*/
				case "progress_circle":
					$dataInit = ['show_icon','percent_fontweight','title_fontweight'];
					$data = $this->reInitData($data, $dataInit);
					$content = '{{block class="Magento\Framework\View\Element\Template" fbuilder_subtitle="'.$this->encodeHtml($data['setting']['subtitle']).'" percent="'.$data['setting']['percent'].'" percent_font_size="'.$data['setting']['percent_font_size'].'" percent_color="'.$data['setting']['percent_color'].'" percent_fontweight="'.$data['setting']['percent_fontweight'].'" title_font_size="'.$data['setting']['title_font_size'].'" title_color="'.$data['setting']['title_color'].'" title_fontweight="'.$data['setting']['title_fontweight'].'" circle_type="'.$data['setting']['circle_type'].'" circle_width="'.$data['setting']['circle_width'].'" show_icon="'.$data['setting']['show_icon'].'" fbuilder_icon="'.$this->encodeHtml($data['setting']['icon']).'" icon_color="'.$data['setting']['icon_color'].'" icon_font_size="'.$data['setting']['icon_font_size'].'" progress_width="'.$data['setting']['progress_width'].'" middle_background="'.$data['setting']['middle_background'].'" bar_background="'.$data['setting']['bar_background'].'" progress_background="'.$data['setting']['progress_background'].'" template="MGS_Fbuilder::widget/progress_circle.phtml"}}'; 
					
					$data['block_content'] = $content;
					$result['message'] = 'success';
					$sessionMessage = __('You saved the Progress Circle block. Please wait for page reload.');
					
					break;
				
				/* Divider Block*/
				case "divider":
					$dataInit = ['show_text','show_icon','text_fontweight'];
					$data = $this->reInitData($data, $dataInit);
					$content = '{{block class="Magento\Framework\View\Element\Template" fbuilder_subtitle="'.$this->encodeHtml($data['setting']['subtitle']).'" width="'.$data['setting']['width'].'" style="'.$data['setting']['style'].'" border_align="'.$data['setting']['border_align'].'" show_text="'.$data['setting']['show_text'].'" text_fontweight="'.$data['setting']['text_fontweight'].'" show_icon="'.$data['setting']['show_icon'].'" fbuilder_icon="'.$this->encodeHtml($data['setting']['icon']).'" template="MGS_Fbuilder::widget/divider.phtml"}}'; 
					
					$data['custom_style_temp']['divider-style'] = [
						'border_width' => $data['setting']['border_width'],
						'border_color' => $data['setting']['border_color'],
						'show_text' => $data['setting']['show_text'],
						'text_font_size' => $data['setting']['text_font_size'],
						'text_color' => $data['setting']['text_color'],
						'text_background' => $data['setting']['text_background'],
						'text_padding' => $data['setting']['text_padding'],
						'show_text' => $data['setting']['show_text'],
						'style' => $data['setting']['style'],
						'show_icon' => $data['setting']['show_icon'],
						'icon_font_size' => $data['setting']['icon_font_size'],
						'icon_color' => $data['setting']['icon_color'],
						'icon_background' => $data['setting']['icon_background'],
						'icon_border' => $data['setting']['icon_border'],
						'icon_padding' => $data['setting']['icon_padding']
					];
					
					$data['block_content'] = $content;
					$result['message'] = 'success';
					$sessionMessage = __('You saved the Divider block. Please wait for page reload.');
					
					break;
				
				/* Divider Block*/
				case "chart":
					
					$content = '';
					if($data['setting']['chart_type']=='line' || $data['setting']['chart_type']=='bar' || $data['setting']['chart_type']=='radar'){
						if(isset($data['setting']['timeline']) && count($data['setting']['timeline'])>0){
							$timeline = $data['setting']['timeline'];
							$timelineArr = [];
							foreach($timeline as $_timeline){
								$timelineArr[] = $this->encodeHtml($_timeline['label']);
							}

							$timelineLabels = implode(',',$timelineArr);		
							$items = json_encode($data['setting']['item']);
							
							$content = '{{block class="MGS\Fbuilder\Block\Widget\Chart" chart_type="'.$data['setting']['chart_type'].'" chart_width="'.$data['setting']['chart_width'].'" fbuilder_timeline_label="'.$this->noSpace($timelineLabels).'" fbuilder_chart_item="'.$this->noSpace($this->encodeHtml($items)).'" template="MGS_Fbuilder::widget/chart.phtml"}}';
							
							$result['message'] = 'success';
						}else{
							$result['message'] = __('No timeline to create chart.');
						}
					}else{
						if(isset($data['setting']['segment']) && count($data['setting']['segment'])>0){	
							$segments = json_encode($data['setting']['segment']);
							
							$content = '{{block class="MGS\Fbuilder\Block\Widget\Chart" chart_type="'.$data['setting']['chart_type'].'" chart_width="'.$data['setting']['chart_width'].'" fbuilder_segment="'.$this->noSpace($this->encodeHtml($segments)).'" template="MGS_Fbuilder::widget/chart.phtml"}}';
							
							$result['message'] = 'success';
						}else{
							$result['message'] = __('No segment to create chart.');
						}
					}
					
					$data['block_content'] = $content;
					
					$sessionMessage = __('You saved the Chart block. Please wait for page reload.');
					
					break;
				
				/* Heading Block*/
				case "heading":
					$dataInit = ['heading_fontweight', 'heading_italic', 'heading_uppercase', 'show_border'];
					$data = $this->reInitData($data, $dataInit);
					$content = '{{block class="Magento\Framework\View\Element\Template" heading="'.$data['setting']['heading'].'" fbuilder_subtitle="'.$this->encodeHtml($data['setting']['subtitle']).'" heading_align="'.$data['setting']['heading_align'].'" heading_fontweight="'.$data['setting']['heading_fontweight'].'" heading_italic="'.$data['setting']['heading_italic'].'" heading_uppercase="'.$data['setting']['heading_uppercase'].'" show_border="'.$data['setting']['show_border'].'" border_style="'.$data['setting']['border_style'].'" border_position="'.$data['setting']['border_position'].'" template="MGS_Fbuilder::widget/heading.phtml"}}'; 
					
					$data['custom_style_temp']['heading-style'] = [
						'heading_font_size' => $data['setting']['heading_font_size'],
						'heading_color' => $data['setting']['heading_color'],
						'heading_background' => $data['setting']['heading_background'],
						'show_border' => $data['setting']['show_border'],
						'border_position' => $data['setting']['border_position'],
						'border_color' => $data['setting']['border_color'],
						'border_container_width' => $data['setting']['border_container_width'],
						'border_width' => $data['setting']['border_width'],
						'border_margin' => $data['setting']['border_margin']
					];
					
					$data['block_content'] = $content;
					$result['message'] = 'success';
					$sessionMessage = __('You saved the Heading block. Please wait for page reload.');
					break;
				
				/* List Block*/
				case "list":
					if(isset($data['setting']['accordion']) && count($data['setting']['accordion'])>0){
						$accordions = $data['setting']['accordion'];

						$dataInit = ['fontweight', 'fontitalic'];
						$data = $this->reInitData($data, $dataInit);
						
						$accordionContent = $accordionLabel = [];
						foreach($accordions as $accordion){
							$accordionContent[] = $this->encodeHtml($accordion['content']);
						}
						
						$accordionData = implode(',',$accordionContent);
						
						$content = '{{block class="Magento\Framework\View\Element\Template" accordion_content="'.$accordionData.'" list_type="'.$data['setting']['list_type'].'" fontweight="'.$data['setting']['fontweight'].'" fontitalic="'.$data['setting']['fontitalic'].'" list_style_type="'.$data['setting']['list_style_type'].'" fbuilder_icon="'.$this->encodeHtml($data['setting']['icon']).'" list_style="'.$data['setting']['list_style'].'"';
						
						$content .= ' template="MGS_Fbuilder::widget/list.phtml"}}';
						
						$data['custom_style_temp']['list-style'] = [
							'font_size' => $data['setting']['font_size'],
							'color' => $data['setting']['color'],
							'margin_bottom' => $data['setting']['margin_bottom'],
							'list_style_type' => $data['setting']['list_style_type'],
							'icon_color' => $data['setting']['icon_color'],
							'icon_font_size' => $data['setting']['icon_font_size'],
							'icon_margin' => $data['setting']['icon_margin']
							
						];
						
						$data['block_content'] = $content;
						$result['message'] = 'success';
						$sessionMessage = __('You saved the List block. Please wait for page reload.');
					}else{
						$result['message'] = __('Have no item to save.');
					}
					break;
				
				/* Lookbook Block*/
				case "lookbook":
					$content = '{{widget type="MGS\Lookbook\Block\Widget\Lookbook" lookbook_id="'.$data['setting']['lookbook_id'].'" template="MGS_Lookbook::widget/lookbook.phtml"}}'; 

					$data['block_content'] = $content;
					$result['message'] = 'success';
					$sessionMessage = __('You saved the Lookbook block. Please wait for page reload.');
					break;
				
				/* Lookbook Block*/
				case "lookbook_slider":
					$content = '{{widget type="MGS\Lookbook\Block\Widget\Slider" slider_id="'.$data['setting']['slide_id'].'" template="MGS_Lookbook::widget/slider.phtml"}}';

					$data['block_content'] = $content;
					$result['message'] = 'success';
					$sessionMessage = __('You saved the Lookbook slider block. Please wait for page reload.');
					break;
				
				/* Latest Post Block*/
				case "latest_post":
					$dataInit = ['use_slider', 'autoplay', 'stop_auto', 'rtl', 'navigation', 'loop', 'pagination', 'hide_thumbnail', 'hide_description', 'hide_create', 'hide_nav'];
					$data = $this->reInitData($data, $dataInit);
					$categories = '';
					
					if(isset($data['setting']['post_category'])){
						$categories = implode(',',$data['setting']['post_category']);
					}else{
						$data['setting']['post_category'] = [];
					}
					
					$content = '{{widget type="MGS\Blog\Block\Widget\Latest" limit="'.$data['setting']['limit'].'" post_category="'.$categories.'" use_slider="'.$data['setting']['use_slider'].'" hide_thumbnail="'.$data['setting']['hide_thumbnail'].'" hide_description="'.$data['setting']['hide_description'].'" character_count="'.$data['setting']['character_count'].'" hide_create="'.$data['setting']['hide_create'].'"';
					
					if($data['setting']['template']=='list.phtml'){
						$content .= ' numbercol="'.$data['setting']['numbercol'].'" percol="'.$data['setting']['percol'].'"';
					}
					
					if($data['setting']['template']=='grid.phtml'){
						$content .= ' perrow="'.$data['setting']['perrow'].'" perrow_tablet="'.$data['setting']['perrow_tablet'].'" perrow_mobile="'.$data['setting']['perrow_mobile'].'"';
					}
					
					if($data['setting']['use_slider']){
						$content .= ' autoplay="'.$data['setting']['autoplay'].'" stop_auto="'.$data['setting']['stop_auto'].'" navigation="'.$data['setting']['navigation'].'" hide_nav="'.$data['setting']['hide_nav'].'" nav_top="'.$data['setting']['nav_top'].'" navigation_position="'.$data['setting']['navigation_position'].'" pagination_position="'.$data['setting']['pagination_position'].'" pagination="'.$data['setting']['pagination'].'" number_row="'.$data['setting']['number_row'].'" slide_by="'.$data['setting']['slide_by'].'" loop="'.$data['setting']['loop'].'" rtl="'.$data['setting']['rtl'].'" slide_margin="'.$data['setting']['slide_margin'].'"';
					}
					
					$content .= ' template="MGS_Fbuilder::widget/blog/'.$data['setting']['template'].'"}}';
					
					$data['block_content'] = $content;
					
					$result['message'] = 'success';
					$sessionMessage = __('You saved the Latest Post block. Please wait for page reload.');
					break;
				
				/* Portfolio Block*/
				case "portfolio":
					$dataInit = ['use_slider', 'autoplay', 'stop_auto', 'rtl', 'navigation', 'loop', 'pagination', 'hide_categories', 'hide_nav'];
					$data = $this->reInitData($data, $dataInit);
					$categories = '';
					if(isset($data['setting']['category_ids'])){
						$categories = implode(',',$data['setting']['category_ids']);
					}else{
						$data['setting']['category_ids'] = [];
					}
					
					$content = '{{block class="MGS\Portfolio\Block\Widget" limit="'.$data['setting']['limit'].'" category_ids="'.$categories.'" use_slider="'.$data['setting']['use_slider'].'" hide_categories="'.$data['setting']['hide_categories'].'" perrow="'.$data['setting']['perrow'].'" perrow_tablet="'.$data['setting']['perrow_tablet'].'" perrow_mobile="'.$data['setting']['perrow_mobile'].'"';

					if($data['setting']['use_slider']){
						$content .= ' autoplay="'.$data['setting']['autoplay'].'" stop_auto="'.$data['setting']['stop_auto'].'" navigation="'.$data['setting']['navigation'].'" hide_nav="'.$data['setting']['hide_nav'].'" nav_top="'.$data['setting']['nav_top'].'" navigation_position="'.$data['setting']['navigation_position'].'" pagination_position="'.$data['setting']['pagination_position'].'" pagination="'.$data['setting']['pagination'].'" number_row="'.$data['setting']['number_row'].'" slide_by="'.$data['setting']['slide_by'].'" loop="'.$data['setting']['loop'].'" rtl="'.$data['setting']['rtl'].'" slide_margin="'.$data['setting']['slide_margin'].'"';
					}
					
					$content .= ' template="MGS_Fbuilder::widget/portfolio.phtml"}}';
					
					$data['block_content'] = $content;
					
					$result['message'] = 'success';
					$sessionMessage = __('You saved the Portfolio block. Please wait for page reload.');
					break;
				
				/* Testimonial Block*/
				case "testimonial":
					$dataInit = ['use_slider', 'autoplay', 'stop_auto', 'rtl', 'navigation', 'loop', 'pagination', 'hide_photo', 'hide_name', 'hide_info', 'content_italic', 'hide_nav'];
					$data = $this->reInitData($data, $dataInit);

					
					$content = '{{block class="MGS\Testimonial\Block\Testimonial" testimonials_count="'.$data['setting']['limit'].'" hide_photo="'.$data['setting']['hide_photo'].'" hide_name="'.$data['setting']['hide_name'].'" hide_info="'.$data['setting']['hide_info'].'" content_italic="'.$data['setting']['content_italic'].'" use_slider="'.$data['setting']['use_slider'].'" perrow="'.$data['setting']['perrow'].'" perrow_tablet="'.$data['setting']['perrow_tablet'].'" perrow_mobile="'.$data['setting']['perrow_mobile'].'"';

					if($data['setting']['use_slider']){
						$content .= ' autoplay="'.$data['setting']['autoplay'].'" stop_auto="'.$data['setting']['stop_auto'].'" navigation="'.$data['setting']['navigation'].'" hide_nav="'.$data['setting']['hide_nav'].'" nav_top="'.$data['setting']['nav_top'].'" navigation_position="'.$data['setting']['navigation_position'].'" pagination_position="'.$data['setting']['pagination_position'].'" pagination="'.$data['setting']['pagination'].'" number_row="'.$data['setting']['number_row'].'" slide_by="'.$data['setting']['slide_by'].'" loop="'.$data['setting']['loop'].'" rtl="'.$data['setting']['rtl'].'" slide_margin="'.$data['setting']['slide_margin'].'"';
					}
					
					$content .= ' template="MGS_Fbuilder::widget/tetimonials.phtml"}}';
					
					$data['custom_style_temp']['testimonial'] = [
						'name_font_size' => $data['setting']['name_font_size'],
						'name_color' => $data['setting']['name_color'],
						'info_font_size' => $data['setting']['info_font_size'],
						'info_color' => $data['setting']['info_color'],
						'content_font_size' => $data['setting']['content_font_size'],
						'content_color' => $data['setting']['content_color']
					];
					
					$data['block_content'] = $content;
					
					$result['message'] = 'success';
					$sessionMessage = __('You saved the Testimonial block. Please wait for page reload.');
					break;
				
				/* Image Block*/
				case "image":
					$dataInit = ['lightbox', 'box_shadown'];
					$data = $this->reInitData($data, $dataInit);
					
					if(isset($_FILES['image']) && $_FILES['image']['name'] != '') {
						try {
							$uploader = $this->uploadFile('image', 'images');
							$data['setting']['image'] = $uploader->getUploadedFileName();
						} catch (\Exception $e) {
							$result['message'] = $e->getMessage();
						}
					}
					if($data['setting']['image_block_type']=='multiple'){
						if(isset($_FILES['image_after']) && $_FILES['image_after']['name'] != '') {
							try {
								$uploader = $this->uploadFile('image_after', 'images');
								$data['setting']['after_image'] = $uploader->getUploadedFileName();
							} catch (\Exception $e) {
								$result['message'] = $e->getMessage();
							}
						}
					}
					
					$content = '{{block class="Magento\Framework\View\Element\Template" type="'.$data['setting']['image_block_type'].'" image="'.$data['setting']['image'].'" box_shadown="'.$data['setting']['box_shadown'].'" url="'.$data['setting']['url'].'"';

					if($data['setting']['image_block_type']=='multiple'){
						$content .= ' after_image="'.$data['setting']['after_image'].'" multiple_effect="'.$data['setting']['multiple_effect'].'" slide_type="'.$data['setting']['slide_type'].'"';
					}else{
						$content .= ' effect="'.$data['setting']['effect'].'" lightbox="'.$data['setting']['lightbox'].'" lightbox_group="'.$data['setting']['lightbox_group'].'"';
						
						
					}
					
					$data['custom_style_temp']['image'] = [
						'border_width' => $data['setting']['border_width'],
						'border_color' => $data['setting']['border_color'],
						'border_radius' => $data['setting']['border_radius']
					];
					
					$content .= ' template="MGS_Fbuilder::widget/image/'.$data['setting']['image_block_type'].'.phtml"}}';
					
					
					
					
					$data['block_content'] = $content;
					$result['message'] = 'success';
					$sessionMessage = __('You saved the Image block. Please wait for page reload.');
					
					break;
				
				/* Button Block*/
				case "button":
					if(trim($data['setting']['subtitle'])==''){
						$result['message'] = __('Have no button to save.');
					}else{
						$dataInit = ['use_border', 'border_top','border_right','border_bottom','border_left','use_icon','use_divider','box_shadow','full_width', 'button_bg_gradient'];
						$data = $this->reInitData($data, $dataInit);
						
						$content = '{{block class="Magento\Framework\View\Element\Template" fbuilder_subtitle="'.$this->encodeHtml($data['setting']['subtitle']).'" use_icon="'.$data['setting']['use_icon'].'" fbuilder_icon="'.$this->encodeHtml($data['setting']['icon']).'" icon_align="'.$data['setting']['icon_align'].'" use_divider="'.$data['setting']['use_divider'].'" box_shadow="'.$data['setting']['box_shadow'].'" button_align="'.$data['setting']['button_align'].'" full_width="'.$data['setting']['full_width'].'" button_link="'.$data['setting']['button_link'].'"';
						
						$data['custom_style_temp']['button'] = [
							'text_font_size' => $data['setting']['text_font_size'],
							'text_color' => $data['setting']['text_color'],
							'text_hover_color' => $data['setting']['text_hover_color'],
							'button_bg_gradient' => $data['setting']['button_bg_gradient'],
							'button_bg_color' => $data['setting']['button_bg_color'],
							'button_bg_hover_color' => $data['setting']['button_bg_hover_color'],
							'button_bg_from' => $data['setting']['button_bg_from'],
							'button_bg_to' => $data['setting']['button_bg_to'],
							'button_bg_orientation' => $data['setting']['button_bg_orientation'],
							'button_bg_hover_from' => $data['setting']['button_bg_hover_from'],
							'button_bg_hover_to' => $data['setting']['button_bg_hover_to'],
							'button_bg_hover_orientation' => $data['setting']['button_bg_hover_orientation'],
							'use_border' => $data['setting']['use_border'],
							'border_color' => $data['setting']['border_color'],
							'border_width' => $data['setting']['border_width'],
							'border_hover_color' => $data['setting']['border_hover_color'],
							'border_top' => $data['setting']['border_top'],
							'border_right' => $data['setting']['border_right'],
							'border_bottom' => $data['setting']['border_bottom'],
							'border_left' => $data['setting']['border_left'],
							'use_icon' => $data['setting']['use_icon'],
							'icon' => $data['setting']['icon'],
							'icon_font_size' => $data['setting']['icon_font_size'],
							'icon_color' => $data['setting']['icon_color'],
							'icon_hover_color' => $data['setting']['icon_hover_color'],
							'icon_align' => $data['setting']['icon_align'],
							'use_divider' => $data['setting']['use_divider'],
							'divider_width' => $data['setting']['divider_width'],
							'divider_color' => $data['setting']['divider_color'],
							'divider_hover_color' => $data['setting']['divider_hover_color'],
							'border_radius' => $data['setting']['border_radius'],
							'full_width' => $data['setting']['full_width'],
							'button_width' => $data['setting']['button_width'],
							'button_height' => $data['setting']['button_height']
						];
						
						$content .= ' template="MGS_Fbuilder::widget/button.phtml"}}';

						
						$data['block_content'] = $content;
						$result['message'] = 'success';
						$sessionMessage = __('You saved the Button block. Please wait for page reload.');
					}
					
					break;
				
				/* Table Block*/
				case "table":
					if(trim($data['text_content'])==''){
						$result['message'] = __('Have no content to save.');
					}else{
						$dataInit = ['fullwidth', 'border_vertical','border_horizontal','other_border_vertical','other_border_horizontal','heading_font_bold'];
						$data = $this->reInitData($data, $dataInit);
						
						$content = $data['text_content'];
						$content = str_replace('<table class="mgs-table-block" ','<table ',$content);
						$content = str_replace('<table ','<table class="mgs-table-block" ',$content);
						
						$data['custom_style_temp']['table'] = [
							'text_align' => $data['setting']['text_align'],
							'border_color' => $data['setting']['border_color'],
							'border_width' => $data['setting']['border_width'],
							'text_color' => $data['setting']['text_color'],
							'font_size' => $data['setting']['font_size'],
							'row_height' => $data['setting']['row_height'],
							'fullwidth' => $data['setting']['fullwidth'],
							'heading_background' => $data['setting']['heading_background'],
							'heading_text_color' => $data['setting']['heading_text_color'],
							'heading_font_size' => $data['setting']['heading_font_size'],
							'heading_font_bold' => $data['setting']['heading_font_bold'],
							'heading_row_height' => $data['setting']['heading_row_height'],
							'heading_border_color' => $data['setting']['heading_border_color'],
							'heading_border_width' => $data['setting']['heading_border_width'],
							'border_vertical' => $data['setting']['border_vertical'],
							'border_horizontal' => $data['setting']['border_horizontal'],
							'other_border_color' => $data['setting']['other_border_color'],
							'other_border_width' => $data['setting']['other_border_width'],
							'other_border_vertical' => $data['setting']['other_border_vertical'],
							'other_border_horizontal' => $data['setting']['other_border_horizontal'],
							'even_background' => $data['setting']['even_background'],
							'odd_background' => $data['setting']['odd_background'],
						];
						
						
						$data['block_content'] = $content;
						$result['message'] = 'success';
						$sessionMessage = __('You saved the Table block. Please wait for page reload.');
					}
					break;
				
				/* Masonry Block*/
				case "masonry":
					$dataInit = ['lightbox','box_shadow'];
					$data = $this->reInitData($data, $dataInit);
					
					if(isset($data['setting']['removeimg']) && count($data['setting']['removeimg'])>0){
						foreach($data['setting']['removeimg'] as $imageName){
							$filePath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('wysiwyg/masonry/') . $imageName;
							if ($this->_file->isExists($filePath))  {
								$this->_file->deleteFile($filePath);
							}
							
							if(isset($data['setting']['addimg']) && count($data['setting']['addimg'])>0){
								$key = array_search ($imageName, array_combine(range(1, count($settings['addimg'])), array_values($settings['addimg'])));
								if($key){
									unset($data['setting']['addimg'][$key]);
								}
							}
							
							if(isset($data['setting']['url']) && count($data['setting']['url'])>0){
								$keyUrl = array_search ($imageName, array_combine(range(1, count($settings['url'])), array_values($settings['url'])));
								if($keyUrl){
									unset($data['setting']['url'][$key]);
								}
							}
						}
					}
					
					if(isset($data['setting']['addimg']) && count($data['setting']['addimg'])>0){
						
						$images = implode(',',$data['setting']['addimg']);
						$urls = implode(',',$data['setting']['url']);
					
						$content = '{{block class="Magento\Framework\View\Element\Template" images="'.$images.'" links="'.$urls.'" column="'.$data['setting']['column'].'" item_margin="'.$data['setting']['item_margin'].'" lightbox="'.$data['setting']['lightbox'].'" box_shadow="'.$data['setting']['box_shadow'].'" effect="'.$data['setting']['effect'].'"  template="MGS_Fbuilder::widget/masonry.phtml"}}';
						
						$data['custom_style_temp']['masonry'] = [
							'border_color' => $data['setting']['border_color'],
							'border_width' => $data['setting']['border_width'],
							'border_radius' => $data['setting']['border_radius']
						];
						
						$data['block_content'] = $content;
						$result['message'] = 'success';
						$sessionMessage = __('You saved the Masonry block. Please wait for page reload.');
					}else{
						$result['message'] = __('Have no image to add to gallery.');
					}
					break;
				
				/* Tabs Block*/
				case "static_tabs":
					if(isset($data['setting']['accordion']) && count($data['setting']['accordion'])>0){
						$accordions = $data['setting']['accordion'];

						$dataInit = ['tab_font_bold', 'tab_italic', 'tab_uppercase'];
						$data = $this->reInitData($data, $dataInit);
						
						usort($accordions, function ($item1, $item2) {
							if ($item1['position'] == $item2['position']) return 0;
							return $item1['position'] < $item2['position'] ? -1 : 1;
						});
						
						$accordionContent = $accordionLabel = [];
						foreach($accordions as $accordion){
							$accordionContent[] = $this->encodeHtml($accordion['content']);
							$accordionLabel[] = $this->encodeHtml($accordion['label']);
						}
						
						$accordionData = implode(',',$accordionContent);
						$labels = implode(',',$accordionLabel);
						
						$content = '{{block class="Magento\Framework\View\Element\Template" accordion_content="'.$accordionData.'" accordion_label="'.$labels.'" tab_style="'.$data['setting']['tab_style'].'"';
						
						if($data['setting']['tab_font_bold']){
							$content .= ' tab_font_bold="1"';
						}
						if($data['setting']['tab_italic']){
							$content .= ' tab_italic="1"';
						}
						if($data['setting']['tab_uppercase']){
							$content .= ' tab_uppercase="1"';
						}
						$content .= ' tab_align="'.$data['setting']['tab_align'].'"';
						
						$data['custom_style_temp']['tab-style'] = ['tab-'.$data['setting']['tab_style']=>[
								'font-size' => $data['setting']['font_size'],
								'primary-color' => $data['setting']['tab_primary_color'],
								'secondary-color' => $data['setting']['tab_secondary_color'],
								'third-color' => $data['setting']['tab_third_color']
							]
						];
						
						$content .= ' template="MGS_Fbuilder::widget/static_tabs.phtml"}}';
						
						$data['block_content'] = $content;
						$result['message'] = 'success';
						$sessionMessage = __('You saved the Tabs block. Please wait for page reload.');
					}else{
						$result['message'] = __('Have no tabs to save.');
					}
					break;
					
					/* Modal Popup Block*/
				case "modal_popup":
				
					if(trim($data['setting']['text_content'])==''){
						$result['message'] = __('Have no content for popup.');
					}elseif(trim($data['setting']['subtitle'])==''){
						$result['message'] = __('Have no button to save.');
					}else{
						$dataInit = ['use_border', 'border_top','border_right','border_bottom','border_left','use_icon','use_divider','box_shadow','full_width', 'button_bg_gradient'];
						$data = $this->reInitData($data, $dataInit);
						
						$content = '{{block class="Magento\Framework\View\Element\Template" fbuilder_text_content="'.$this->encodeHtml($data['setting']['text_content']).'" fbuilder_subtitle="'.$this->encodeHtml($data['setting']['subtitle']).'" use_icon="'.$data['setting']['use_icon'].'" fbuilder_icon="'.$this->encodeHtml($data['setting']['icon']).'" icon_align="'.$data['setting']['icon_align'].'" use_divider="'.$data['setting']['use_divider'].'" box_shadow="'.$data['setting']['box_shadow'].'" button_align="'.$data['setting']['button_align'].'" full_width="'.$data['setting']['full_width'].'" generate_block_id="'.$data['setting']['generate_block_id'].'" fbuilder_button_text="'.$this->encodeHtml($data['setting']['button_text']).'"';
						
						$data['custom_style_temp']['button'] = [
							'text_font_size' => $data['setting']['text_font_size'],
							'text_color' => $data['setting']['text_color'],
							'text_hover_color' => $data['setting']['text_hover_color'],
							'button_bg_gradient' => $data['setting']['button_bg_gradient'],
							'button_bg_color' => $data['setting']['button_bg_color'],
							'button_bg_hover_color' => $data['setting']['button_bg_hover_color'],
							'button_bg_from' => $data['setting']['button_bg_from'],
							'button_bg_to' => $data['setting']['button_bg_to'],
							'button_bg_orientation' => $data['setting']['button_bg_orientation'],
							'button_bg_hover_from' => $data['setting']['button_bg_hover_from'],
							'button_bg_hover_to' => $data['setting']['button_bg_hover_to'],
							'button_bg_hover_orientation' => $data['setting']['button_bg_hover_orientation'],
							'use_border' => $data['setting']['use_border'],
							'border_color' => $data['setting']['border_color'],
							'border_width' => $data['setting']['border_width'],
							'border_hover_color' => $data['setting']['border_hover_color'],
							'border_top' => $data['setting']['border_top'],
							'border_right' => $data['setting']['border_right'],
							'border_bottom' => $data['setting']['border_bottom'],
							'border_left' => $data['setting']['border_left'],
							'use_icon' => $data['setting']['use_icon'],
							'icon' => $data['setting']['icon'],
							'icon_font_size' => $data['setting']['icon_font_size'],
							'icon_color' => $data['setting']['icon_color'],
							'icon_hover_color' => $data['setting']['icon_hover_color'],
							'icon_align' => $data['setting']['icon_align'],
							'use_divider' => $data['setting']['use_divider'],
							'divider_width' => $data['setting']['divider_width'],
							'divider_color' => $data['setting']['divider_color'],
							'divider_hover_color' => $data['setting']['divider_hover_color'],
							'border_radius' => $data['setting']['border_radius'],
							'full_width' => $data['setting']['full_width'],
							'button_width' => $data['setting']['button_width'],
							'button_height' => $data['setting']['button_height']
						];
						
						$data['custom_style_temp']['popup'] = [
							'generate_block_id' => $data['setting']['generate_block_id'],
							'popup_width' => $data['setting']['popup_width'],
							'popup_background' => $data['setting']['popup_background'],
							'popup_font_size' => $data['setting']['popup_font_size'],
							'popup_color' => $data['setting']['popup_color'],
							'popup_border_radius' => $data['setting']['popup_border_radius'],
							'title_font_size' => $data['setting']['title_font_size'],
							'title_color' => $data['setting']['title_color'],
							'title_boder_color' => $data['setting']['title_boder_color'],
							'title_border_size' => $data['setting']['title_border_size']
						];
						
						$content .= ' template="MGS_Fbuilder::widget/modal_popup.phtml"}}';

						
						$data['block_content'] = $content;
						$result['message'] = 'success';
						$sessionMessage = __('You saved the Modal Popup block. Please wait for page reload.');
					}
					
					break;
				
				/* Form Block*/
				case "form":
					if(isset($data['setting']['form']) && count($data['setting']['form'])>0){
						
						$dataInit = ['use_border', 'border_top','border_right','border_bottom','border_left','use_icon','use_divider','box_shadow','full_width', 'button_bg_gradient','use_mgs_captcha'];
						$data = $this->reInitData($data, $dataInit);
						
						$fields = $data['setting']['form'];

						usort($fields, function ($item1, $item2) {
							if ($item1['position'] == $item2['position']) return 0;
							return $item1['position'] < $item2['position'] ? -1 : 1;
						});
						
						$content = '{{block class="MGS\Fbuilder\Block\Widget\Form" fields="'.$this->encodeHtml(json_encode($fields)).'" fbuilder_subtitle="'.$this->encodeHtml($data['setting']['subtitle']).'" use_icon="'.$data['setting']['use_icon'].'" fbuilder_icon="'.$this->encodeHtml($data['setting']['icon']).'" icon_align="'.$data['setting']['icon_align'].'" use_divider="'.$data['setting']['use_divider'].'" box_shadow="'.$data['setting']['box_shadow'].'" button_align="'.$data['setting']['button_align'].'" full_width="'.$data['setting']['full_width'].'" use_mgs_captcha="'.$data['setting']['use_mgs_captcha'].'" mgs_receive_email="'.$data['setting']['mgs_receive_email'].'" mgs_email_subject="'.$data['setting']['mgs_email_subject'].'" mgs_email_template_top="'.$this->encodeHtml($data['setting']['mgs_email_template_top']).'" mgs_email_template_bottom="'.$this->encodeHtml($data['setting']['mgs_email_template_bottom']).'" mgs_success_message="'.$this->encodeHtml($data['setting']['mgs_success_message']).'"';			
						
						$content .= ' template="MGS_Fbuilder::widget/form.phtml"}}';
						
						$data['custom_style_temp']['button'] = [
							'text_font_size' => $data['setting']['text_font_size'],
							'text_color' => $data['setting']['text_color'],
							'text_hover_color' => $data['setting']['text_hover_color'],
							'button_bg_gradient' => $data['setting']['button_bg_gradient'],
							'button_bg_color' => $data['setting']['button_bg_color'],
							'button_bg_hover_color' => $data['setting']['button_bg_hover_color'],
							'button_bg_from' => $data['setting']['button_bg_from'],
							'button_bg_to' => $data['setting']['button_bg_to'],
							'button_bg_orientation' => $data['setting']['button_bg_orientation'],
							'button_bg_hover_from' => $data['setting']['button_bg_hover_from'],
							'button_bg_hover_to' => $data['setting']['button_bg_hover_to'],
							'button_bg_hover_orientation' => $data['setting']['button_bg_hover_orientation'],
							'use_border' => $data['setting']['use_border'],
							'border_color' => $data['setting']['border_color'],
							'border_width' => $data['setting']['border_width'],
							'border_hover_color' => $data['setting']['border_hover_color'],
							'border_top' => $data['setting']['border_top'],
							'border_right' => $data['setting']['border_right'],
							'border_bottom' => $data['setting']['border_bottom'],
							'border_left' => $data['setting']['border_left'],
							'use_icon' => $data['setting']['use_icon'],
							'icon' => $data['setting']['icon'],
							'icon_font_size' => $data['setting']['icon_font_size'],
							'icon_color' => $data['setting']['icon_color'],
							'icon_hover_color' => $data['setting']['icon_hover_color'],
							'icon_align' => $data['setting']['icon_align'],
							'use_divider' => $data['setting']['use_divider'],
							'divider_width' => $data['setting']['divider_width'],
							'divider_color' => $data['setting']['divider_color'],
							'divider_hover_color' => $data['setting']['divider_hover_color'],
							'border_radius' => $data['setting']['border_radius'],
							'full_width' => $data['setting']['full_width'],
							'button_width' => $data['setting']['button_width'],
							'button_height' => $data['setting']['button_height']
						];
						
						$data['block_content'] = $content;
						$result['message'] = 'success';
						$sessionMessage = __('You saved the Form block. Please wait for page reload.');
					}else{
						$result['message'] = __('Have no tabs to save.');
					}
					break;
			}
			if($result['message']=='success'){
				$this->saveBlockData($data, $sessionMessage);
				$this->cacheManager->clean(['full_page']);
			}else{
				return $this->getMessageHtml('danger', $result['message'], false);
			}
		}
		else{
			$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
			$resultRedirect->setUrl($this->_redirect->getRefererUrl());
			return $resultRedirect;
		}
    }
}
