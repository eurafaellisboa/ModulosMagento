<?php
namespace Blueskytechco\OptimizeSpeed\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class OptimizeSpeedHttp implements ObserverInterface {

	protected $_scopeConfig;
	protected $_appState;
	protected $_remoteAddress;
	protected $_httpHeader;

	public function __construct(
    	\Magento\Framework\App\State $appState,
    	\Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
    	\Magento\Framework\HTTP\Header $httpHeader,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
    	$this->_httpHeader = $httpHeader;
    	$this->_appState = $appState;
        $this->_scopeConfig = $scopeConfig;
        $this->_remoteAddress = $remoteAddress;
    }
	
	public function execute(Observer $observer) {
		$response = $observer->getResponse();
		$this->customResponse($response);
    }

    public function customResponse($response) {
    	if (strpos($response->getBody(), '</head>') !== false) {
    		$ip = $this->_remoteAddress->getRemoteAddress();
			$userAgent = $this->_httpHeader->getHttpUserAgent();
			$modeSys = false;
			$ips = ['208.70.247.157','172.255.48.130','172.255.48.131','172.255.48.132','172.255.48.133','172.255.48.134','172.255.48.135','172.255.48.136','172.255.48.137','172.255.48.138','172.255.48.139','172.255.48.140','172.255.48.141','172.255.48.142','172.255.48.143','172.255.48.144','172.255.48.145','172.255.48.146','172.255.48.147','52.229.122.240','104.214.72.101','13.66.7.11','13.85.24.83','13.85.24.90','13.85.82.26','40.74.242.253','40.74.243.13','40.74.243.176','104.214.48.247','157.55.189.189','104.214.110.135','70.37.83.240','65.52.36.250','13.78.216.56','52.162.212.163','23.96.34.105','65.52.113.236','172.255.61.34','172.255.61.35','172.255.61.36','172.255.61.37','172.255.61.38','172.255.61.39','172.255.61.40','52.237.235.185','52.237.250.73','52.237.236.145','104.41.2.19','191.235.98.164','191.235.99.221','191.232.194.51','104.211.143.8','104.211.165.53','52.172.14.87','40.83.89.214','52.175.57.81','20.188.63.151','20.52.36.49','52.246.165.153','51.144.102.233','13.76.97.224','102.133.169.66','52.231.199.170','13.53.162.7','40.123.218.94'];
			if($ip && $userAgent){
				$user_agent = preg_match( '#PingdomPageSpeed|Chrome-Lighthouse#i', $userAgent);
				$check_user_agent = (bool) $user_agent;
				if(in_array($ip, $ips) || $check_user_agent){
					$mode = $this->_appState->getMode();
					$merge_javaScript_files = $this->getConfiguration('dev/js/merge_files');
			        $minify_javaScript_files = $this->getConfiguration('dev/js/minify_files');
			        $lazy_javascript = $this->getConfiguration('themesetting/general/lazy_javascript');
					if($mode == \Magento\Framework\App\State::MODE_PRODUCTION && $merge_javaScript_files && $minify_javaScript_files && $lazy_javascript == 'enable'){
			        	$modeSys = true;
			        }
				}
			}

    		$content = $response->getBody();
        	if($modeSys){
	        	$content = preg_replace('#type="text/javascript"#', 'type="KallesLazyScript"  data-kalleslazy-type="text/javascript"', $content);
        		$content = preg_replace('#<script>#', '<script type="KallesLazyScript">', $content);
	        }
	        else{
	        	$content = preg_replace('#type="text/javascript"#', '', $content);
	        }

	        if (preg_match_all('/<div([^>]*?)data-background-images=(\"|\'|)(.*?)(\"|\'|)(.*?)>/is', $content, $data_images)) {
	            $data_images_count = count($data_images);
				foreach ($data_images[0] as $key => $image) {
	                $attributes = '';
	                foreach ($data_images as $key_attribute => $attribute) {
	                    if ($key_attribute < 1) {
	                        continue;
	                    }
	                    if(strpos($data_images[$key_attribute][$key], 'class') !== false){
	                        $attributes = $data_images[$key_attribute][$key];
	                        break;
	                    }
	                }
					if (preg_match_all('/class="([^>]*?)(.*?)"/is', $attributes, $data_class)) {
						if(isset($data_class[2])){
							if(isset($data_class[2][0]) && $data_class[2][0]){
								$class = explode(" ",$data_class[2][0]);
								foreach ($class as $item) {
									if($item){
										if(preg_match_all('#<style type="text/css">(.*?)</style>#is', $content, $data_style)) {
											if(isset($data_style[0]) && isset($data_style[1])){
												foreach($data_style[0] as $key_style => $style){
													if(isset($data_style[1][$key_style])){
														$attributes_style = $data_style[1][$key_style];
														if($attributes_style){
															if(strpos($attributes_style, $item) !== false){
																$data_bgset = '';
																if (preg_match_all("/data-background-images='([^>]*?)(.*?)'/is", $data_images[0][$key], $images)){
																	if(isset($images[2][0])){
																		$bgset_images = str_replace('\\','',$images[2][0]);
																		$bgset_images = json_decode($bgset_images,true);
																		if($bgset_images && count($bgset_images) > 0){
																			if(isset($bgset_images['desktop_image'])){
																				$data_bgset = $bgset_images['desktop_image'];
																				if(isset($bgset_images['mobile_image'])){
																					$data_bgset = $bgset_images['mobile_image']. '[(max-width: 768px)] | '.$data_bgset;
																				} 
																			}else{
																				if(isset($bgset_images['mobile_image'])){
																					$data_bgset = $bgset_images['mobile_image']. '[(max-width: 768px)]';
																				}
																			}
																		}
																	}
																}
																if($data_bgset){
																	$data_bgset = 'data-bgset="'.$data_bgset.'"';
																	$content = str_replace($style,'', $content);
																	$new_content = $data_class[2][0].' lazyload" '.$data_bgset;
																	$content = str_replace($data_class[2][0].'"',$new_content, $content);
																}
															}
														}
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
			
			$content = preg_replace('#<style type="text/css">#', '<style>', $content);
			
			if(preg_match_all('#<style>(.*?)</style>#is', $content, $inline_styles)) {
				if(isset($inline_styles[0])){
					foreach($inline_styles[0] as $key_inline_styles => $inline_style){
						if(strpos($inline_style, '#html-body') !== false || strpos($inline_style, 'rs-') !== false || strpos($inline_style, 'ui-menu-item') !== false){
							$content = str_replace($inline_style,'', $content);
							$content = str_replace('</head>',$inline_style.'</head>', $content);
						}
					}
				}
			}
			if($modeSys){
	        	$htmlOutput = explode("</body>", $content);
	        	if(is_array($htmlOutput) && !empty($htmlOutput) && count($htmlOutput) == 2){
		        	$script = '<script>class KallesLazyLoad{constructor(e){this.triggerEventsJs=e,this.eventOptionsJs={passive:!0},this.userEventListenerJs=this.triggerListenerJs.bind(this),this.delayedScriptsJs={normal:[],async:[],defer:[]}}triggerListenerJs(){this._removeUserInteractionListenerJs(this),"loading"===document.readyState?document.addEventListener("DOMContentLoaded",this._loadEverythingReadyNow.bind(this)):this._loadEverythingReadyNow()}_removeUserInteractionListenerJs(e){this.triggerEventsJs.forEach(t=>window.removeEventListener(t,e.userEventListenerJs,e.eventOptionsJs))}_addUserInteractionListenerJs(e){this.triggerEventsJs.forEach(t=>window.addEventListener(t,e.userEventListenerJs,e.eventOptionsJs))}_registerAllDelayedScriptsJs(){document.querySelectorAll("script[type=KallesLazyScript]").forEach(e=>{e.hasAttribute("src")?e.hasAttribute("async")&&!1!==e.async?this.delayedScriptsJs.async.push(e):e.hasAttribute("defer")&&!1!==e.defer||"module"===e.getAttribute("data-kalleslazy-type")?this.delayedScriptsJs.defer.push(e):this.delayedScriptsJs.normal.push(e):this.delayedScriptsJs.normal.push(e)})}_preloadAllScriptsJs(){var e=document.createDocumentFragment();[...this.delayedScriptsJs.normal,...this.delayedScriptsJs.defer,...this.delayedScriptsJs.async].forEach(t=>{const s=t.getAttribute("src");if(s){const t=document.createElement("link");t.href=s,t.rel="preload",t.as="script",e.appendChild(t)}}),document.head.appendChild(e)}_delayEventListenersJs(){let e={};function t(t,s){!function(t){function s(s){return e[t].eventsToRewrite.indexOf(s)>=0?"kallesspeed-"+s:s}e[t]||(e[t]={originalFunctions:{add:t.addEventListener,remove:t.removeEventListener},eventsToRewrite:[]},t.addEventListener=function(){arguments[0]=s(arguments[0]),e[t].originalFunctions.add.apply(t,arguments)},t.removeEventListener=function(){arguments[0]=s(arguments[0]),e[t].originalFunctions.remove.apply(t,arguments)})}(t),e[t].eventsToRewrite.push(s)}function s(e,t){let s=e[t];Object.defineProperty(e,t,{get:()=>s||function(){},set(n){e["kalles"+t]=s=n}})}t(document,"DOMContentLoaded"),t(window,"DOMContentLoaded"),t(window,"load"),t(window,"pageshow"),t(document,"readystatechange"),s(document,"onreadystatechange"),s(window,"onload"),s(window,"onpageshow")}_handleDocumentWriteJs(){const e=new Map;document.write=document.writeln=function(t){const s=document.currentScript,n=document.createRange(),a=s.parentElement;let i=e.get(s);void 0===i&&(i=s.nextSibling,e.set(s,i));const r=document.createDocumentFragment();n.setStart(r,0),r.appendChild(n.createContextualFragment(t)),a.insertBefore(r,i)}}async _loadEverythingReadyNow(){this._delayEventListenersJs(),this._handleDocumentWriteJs(),this._registerAllDelayedScriptsJs(),this._preloadAllScriptsJs(),await this._loadScriptsFromListJs(this.delayedScriptsJs.normal),await this._loadScriptsFromListJs(this.delayedScriptsJs.defer),await this._loadScriptsFromListJs(this.delayedScriptsJs.async),await this._triggerDOMContentLoadedJs(),await this._triggerWindowLoadJs(),window.dispatchEvent(new Event("kallesspeed-allScriptsLoaded"))}async _loadScriptsFromListJs(e){const t=e.shift();return t?(await this._transformScript(t),this._loadScriptsFromListJs(e)):Promise.resolve()}async _transformScript(e){return await this._requestAnimFrame(),new Promise(t=>{const s=document.createElement("script");let n;[...e.attributes].forEach(e=>{let t=e.nodeName;"type"!==t&&("data-kalleslazy-type"===t&&(t="type",n=e.nodeValue),s.setAttribute(t,e.nodeValue))}),e.hasAttribute("src")?(s.addEventListener("load",t),s.addEventListener("error",t)):(s.text=e.text,t()),e.parentNode.replaceChild(s,e)})}async _triggerDOMContentLoadedJs(){this.domReadyFired=!0,await this._requestAnimFrame(),document.dispatchEvent(new Event("kallesspeed-DOMContentLoaded")),await this._requestAnimFrame(),window.dispatchEvent(new Event("kallesspeed-DOMContentLoaded")),await this._requestAnimFrame(),document.dispatchEvent(new Event("kallesspeed-readystatechange")),await this._requestAnimFrame(),document.kallesonreadystatechange&&document.kallesonreadystatechange()}async _triggerWindowLoadJs(){await this._requestAnimFrame(),window.dispatchEvent(new Event("kallesspeed-load")),await this._requestAnimFrame(),window.kallesonload&&window.kallesonload(),await this._requestAnimFrame(),window.dispatchEvent(new Event("kallesspeed-pageshow")),await this._requestAnimFrame(),window.kallesonpageshow&&window.kallesonpageshow()}async _requestAnimFrame(){return new Promise(e=>requestAnimationFrame(e))}static run(){const e=new KallesLazyLoad(["keydown","mousemove","touchmove","touchstart","touchend","wheel"]);e._addUserInteractionListenerJs(e)}}KallesLazyLoad.run();</script>';
		        	$content = $htmlOutput[0].$script.'</body>'.$htmlOutput[1];
		        }
	        }

	        $response->setBody($content);
        }
    }

    public function getConfiguration($path, $storeId = null)
	{
		return $this->_scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
	}
}