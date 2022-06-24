<?php
namespace MGS\Amp\Helper;

class Setting extends \Magento\Framework\App\Helper\AbstractHelper {
	public function decodeHtmlTag($content){
		$result = str_replace("&lt;","<",$content);
		$result = str_replace("&gt;",">",$result);
		$result = str_replace('&#34;','"',$result);
		$result = str_replace("&#39;","'",$result);
		return $result;
	}
	
	public function getAmpCarouselSetting($data){
		$html = 'height="380" layout="fixed-height" type="slides"';
		if(isset($data['autoplay']) && $data['autoplay']){
			$html .= ' autoplay delay="5000"';
		}

		if(isset($data['navigation']) && $data['navigation']){
			$html .= ' controls';
		}
		return $html;
	}
	function getYouTubeVideoId($url) {
		$video_id = false;
		$url = parse_url($url);
		if (strcasecmp($url['host'], 'youtu.be') === 0)
		{
			  #### (dontcare)://youtu.be/<video id>
			  $video_id = substr($url['path'], 1);
		}
		elseif (strcasecmp($url['host'], 'www.youtube.com') === 0)
		{
			  if (isset($url['query']))
			  {
				   parse_str($url['query'], $url['query']);
				   if (isset($url['query']['v']))
				   {
						#### (dontcare)://www.youtube.com/(dontcare)?v=<video id>
						$video_id = $url['query']['v'];
				   }
			   }
			   if ($video_id == false)
			   {
				   $url['path'] = explode('/', substr($url['path'], 1));
				   if (in_array($url['path'][0], array('e', 'embed', 'v')))
				   {
						#### (dontcare)://www.youtube.com/(whitelist)/<video id>
						$video_id = $url['path'][1];
				   }
				}
		}else{
			return false;
		 }
		return $video_id;
	}
	function getVimeoVideoId($url) {
    
        $regs = array();
    
        $id = '';
    
        if (preg_match('%^https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)(?:[?]?.*)$%im', $url, $regs)) {
            $id = $regs[3];
        }
    
        return $id;
    }
}