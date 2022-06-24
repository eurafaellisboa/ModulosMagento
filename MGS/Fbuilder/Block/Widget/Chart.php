<?php

namespace MGS\Fbuilder\Block\Widget;

use Magento\Framework\View\Element\Template;

class Chart extends Template
{
	public function getChartJs($blockId){
		$type = $this->getChartType();
		$html = "var ctx".$blockId." = document.getElementById('mgsChart".$blockId."');";
		$labelHtml = '';
		if($type=='line' || $type=='bar' || $type=='radar'){
			$labels = explode(',',$this->decodeHtmlTag($this->getFbuilderTimelineLabel()));
			$items = json_decode($this->decodeHtmlTag($this->getFbuilderChartItem()), true);

			$labelHtml = "[";
			if(count($labels)>0){
				foreach($labels as $label){
					$labelHtml .= "'".$label."',";
				}
				$labelHtml = substr($labelHtml, 0, -1);
			}
			$labelHtml .= "]";
			

			$dataset = '[';

			if(count($items)>0){
				foreach($items as $key=>$item){
					$data = '['.implode(',',$item['point']).']';
					$dataset .= '{ 
						data: '.$data.',
						label: "'.$item['label'].'",
						borderColor: "'.$item['background'].'",';
					if($type=='radar'){
						list($r, $g, $b) = sscanf($item['background'], "#%02x%02x%02x");
						$dataset .= 'backgroundColor: "rgba('.$r.','.$g.','.$b.',.3)",
							fill: true
						},';
					}else{
						$dataset .= 'backgroundColor: "'.$item['background'].'",
							fill: false
						},';
					}
				}
			}

			$dataset .= ']';
			
			
		}else{
			$segments = json_decode($this->decodeHtmlTag($this->getFbuilderSegment()), true);
			$dataset = '[';

			$data = $background = $label = [];
			if(count($segments)>0){
				foreach($segments as $key=>$segment){
					$data[] = $segment['value'];
					$background[] = '"'.$segment['background'].'"';
					$label[] = '"'.$segment['label'].'"';
				}
				$labelHtml = '['. implode(',',$label) .']';
				$backgroundHtml = '['. implode(',',$background) .']';
				$dataHtml = '['. implode(',',$data) .']';
				
				$dataset .= '{
					"data":'.$dataHtml.',
					"backgroundColor":'.$backgroundHtml.'
				}';
			}

			$dataset .= ']';
		}
		
		$html .= "var myChart".$blockId." = new Chart(ctx".$blockId.", {
			type: '".$type."',
			data: {
				labels: ".$labelHtml.",
				datasets: ".$dataset."
			}
		});";
		
		return $html;
	}
	
	public function decodeHtmlTag($content){
		$result = str_replace("266c746368616e67653b","<",$content);
		$result = str_replace("2667746368616e67653b",">",$result);
		$result = str_replace('262333346368616e67653b','"',$result);
		$result = str_replace("262333396368616e67653b","'",$result);
		$result = str_replace("26636f6d6d616368616e67653b",",",$result);
		$result = str_replace("26706c75736368616e67653b","+",$result);
		$result = str_replace("266c6566746375726c79627261636b65743b","{",$result);
		$result = str_replace("2672696768746375726c79627261636b65743b","}",$result);
		$result = str_replace("266d67735f73706163653b"," ",$result);
		return $result;
	}
}