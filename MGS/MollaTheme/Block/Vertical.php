<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MGS\MollaTheme\Block;

/**
 * Main contact form block
 */
class Vertical extends \MGS\Mmegamenu\Block\Vertical
{
	public function drawList($category, $item, $level = 1) {
        /* $maxLevel = $item->getMaxLevel();
        if ($maxLevel == '' || $maxLevel == NULL) {
            $maxLevel = Mage::getStoreConfig('megamenu/general/max_level');
        }

        if ($maxLevel == 0 || $maxLevel == '' || $maxLevel == NULL) {
            $maxLevel = 100;
        } */
		$maxLevel = 10;

        $children = $this->getSubCategoryAccepp($category->getId(), $item);
        $childrenCount = count($children);

        $htmlLi = '<li';
  
        $htmlCateClass = 'mmegamenu-' . $category->getId();
		$htmlLi .= ' class="level'.$level.' '.$htmlCateClass.'';
        
        if ($childrenCount > 0 && $item->getColumns() == 1) {
            $htmlLi .= ' dropdown-submenu';
			$htmlA = ' onclick="toggleMenu(this)"';
        }else{$htmlA = '';}

        $htmlLi .= '">';
        $html[] = $htmlLi;
		
		 $html[] = '<a href="' . $this->getCategoryUrl($category) . '"'.$htmlA.'>';
        if ($item->getColumns() > 1 && $level == 1) {
            $html[] = '<span class="mega-menu-sub-title">';
			
        }

        $html[] = $category->getName();

		if($category->getMgsMegamenuItemLabel()){
			$backgroundLabel = "";
			if($category->getMgsMegamenuItemBackground()){
				$backgroundLabel = $category->getMgsMegamenuItemBackground();
			}
			if($backgroundLabel != ""){
				$html[] = '<span class="label-menu" style="background-color: '.$backgroundLabel.'; border-color: '.$backgroundLabel.';">';
			}else {
				$html[] = '<span class="label-menu">';
			}
			$html[] = $category->getMgsMegamenuItemLabel();
			$html[] = '</span>';
		}
		
        if ($item->getColumns() > 1 && $level == 1) {
            $html[] = '</span>';
        }
		
		if ($childrenCount > 0 && $item->getColumns() == 1) {
            $html[] = '<span class="icon-next"><i class="fa fa-angle-right">&nbsp;</i></span>';
        }
		
        $html[] = '</a>';

        if ($level < $maxLevel) {


            $maxSub = 50;
			
            $htmlChildren = '';
            if ($childrenCount > 0) {
                $i = 0;
                foreach ($children as $child) {
                    $i++;
                    if ($i <= $maxSub) {
                        $_child = $this->getModel('Magento\Catalog\Model\Category')->load($child);
                        $htmlChildren .= $this->drawList($_child, $item, ($level + 1));
                    }
                }
            }
            if (!empty($htmlChildren)) {
                $html[] = '<span class="toggle-menu"><a onclick="toggleEl(this,\'mobile-menu-cat-' . $category->getId() . '-' . $item->getParentId() . '\')" href="javascript:void(0)" class=""><span class="icon-plus"></span></a></span>';

                $html[] = '<ul id="mobile-menu-cat-' . $category->getId() . '-' . $item->getParentId() . '"';
                if ($item->getColumns() > 1) {
                    $html[] = ' class="sub-menu"';
                } else {
                    $html[] = ' class="dropdown-menu"';
                }
                $html[] = '>';
                $html[] = $htmlChildren;
                $html[] = '</ul>';
            }
        }
		if ($category->getFbuilderThumbnail() !="" && $item['columns'] > 1 && $level == 1 && $item['use_thumbnail'] == 1) {
			$imageCate = $category->getFbuilderThumbnail();
			$imageCateUrl = $this->_urlBuilder->getBaseUrl(
                        ['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]
                    ) . 'catalog/category/' . $imageCate;
			$html[] = '<a class="category-image" href="' . $this->getCategoryUrl($category) . '" title="category-image" ><img class="img-fluid" alt="'.$category->getName().'" src="'.$imageCateUrl.'" /></a>';
        }
        $html[] = '</li>';
        $html = implode("\n", $html);
        return $html;
    }
	public function getCategoryMenu($item) {
        $html = '<a';
        $categoryId = $item->getCategoryId();
        $subCatAccepp = $this->getSubCategoryAccepp($categoryId, $item);
        if ($categoryId) {
            $category = $this->getModel('Magento\Catalog\Model\Category')->load($categoryId);
            $html.=' href="';
            if ($item->getUrl() != '') {
				if (filter_var($item->getUrl(), FILTER_VALIDATE_URL)) { 
					$html = $item->getUrl() . '"';
				}else{
					$html.= $this->getUrl($item->getUrl()) . '"';
				}
				
                $html.= $this->getUrl($item->getUrl()) . '"';
            } else {
                if ($this->getStore()->getRootCategoryId() == $category->getId()) {
                    $html.='#" onclick="toggleMenu(this)"';
                } else {
                    $html.= $this->getCategoryUrl($category) . '"';
                }
            }
			
        }
        $html.=' class="level0';

        if (count($subCatAccepp) > 0) {
            $html.= ' dropdown-toggle';
        }

        $html.='">';
        if ($item->getHtmlLabel() != '') {
            $html.=$item->getHtmlLabel();
        }
        $html.='<span data-hover="'.$item->getTitle().'">'.$item->getTitle().'</span>';
        $html.= '</a>';

        if (count($subCatAccepp) > 0 || $item->getTopContent() != '' || $item->getBottomContent() != '') {
            $html.='<span class="toggle-menu"><a onclick="toggleEl(this,\'mobile-menu-' . $item->getId() . '-'. $item->getParentId() . '\')" href="javascript:void(0)" class=""><span class="icon-plus"></span></a></span>';
            $html.='<ul class="dropdown-menu menu-'.$item->getColumns().'-columns  " id="mobile-menu-' . $item->getId() . '-' . $item->getParentId() . '"><li>';
            $columnAccepp = count($subCatAccepp);
			$arrColumn = [];
            if ($columnAccepp > 0) {
                $columns = $item->getColumns();
				if($columns > 1 && $item->getLeftContent()!='' && $item->getLeftCol()!=0){
					$columns = $columns - $item->getLeftCol();
				}
				
				if($columns > 1 && $item->getRightContent()!='' && $item->getRightCol()!=0){
					$columns = $columns - $item->getRightCol();
				}

                $arrOneElement = array_chunk($subCatAccepp, 1);
                $countCat = count($subCatAccepp);
                $count = 0;
                while ($countCat > 0) {
                    for ($i = 0; $i < $columns; $i++) {
                        if (isset($subCatAccepp[$count])) {
                            $arrColumn[$i][] = $subCatAccepp[$count];
                            $count++;
                        }
                    }
                    $countCat--;
                }

                $newArrColumn = [];
                $newCount = 0;
				
				for ($i = 0; $i < count($arrColumn); $i++) {
					$newColumn = count($arrColumn[$i]);
					for ($j = 0; $j < $newColumn; $j++) {
						$newArrColumn[$i][$j] = $subCatAccepp[$newCount];
						$newCount++;
					}
				}

                $arrColumn = $newArrColumn;

                 $columns = $item->getColumns();

                if ($columns > 1) {
                    $html.= '<div class="mega-menu-content"><div class="line">';

                    if ($item->getTopContent() != '') {
                        $html.='<div class="top_content static-content col-des-12">';
                        $html.= $this->_filterProvider->getBlockFilter()->filter($item->getTopContent());
                        $html.='</div>';
                    }
					
					if($item->getLeftContent()!='' && $item->getLeftCol()!=0){
						$html.='<div class="left_content static-content col-des-'.$this->getColumnByCol($item->getColumns()) * $item->getLeftCol().'">';
                        $html.= $this->_filterProvider->getBlockFilter()->filter($item->getLeftContent());
                        $html.='</div>';
					}
                } else {
                    $html.= '<ul>';
                }
                foreach ($arrColumn as $_arrColumn) {
                    $html.= $this->drawListSub($item, $_arrColumn);
                }
				
				$columns = $item->getColumns();
				
                if ($columns > 1) {
					if($item->getRightContent()!='' && $item->getRightCol()!=0){
						$html.='<div class="right_content static-content col-des-'.$this->getColumnByCol($item->getColumns()) * $item->getRightCol().'">';
                        $html.= $this->_filterProvider->getBlockFilter()->filter($item->getRightContent());
                        $html.='</div>';
					}

                    if ($item->getBottomContent() != '') {
                        $html.='<div class="bottom_content static-content col-des-12">';
                        $html.= $this->_filterProvider->getBlockFilter()->filter($item->getBottomContent());
                        $html.='</div>';
                    }

                    $html.= '</div></div>';
                } else {
                    $html.= '</ul>';
                }
            }


            $html.='</li></ul>';
        }

        return $html;
    }
}

