<?php
/**
 * Raptor Commerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Raptor
 * @package    Raptor_Explodedmenu
 * @copyright  Copyright (c) 2010 Raptor Commerce (http://www.raptorcommerce.com.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog navigation
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Raptor_Explodedmenu_Block_Navigation extends Mage_Catalog_Block_Navigation
{
	public function drawItem($category, $level=0, $last=false) {
		$html = '';
		if (!$category->getIsActive()) {
			return $html;
		}
		$activeChildren = $this->getActiveChildren($category);
		$html.= "<li class='level0'";
		if (sizeof($activeChildren) > 0) {
			$html.= ' onmouseover="toggleMenu(this,1)" onmouseout="toggleMenu(this,0)"';		}
		if ($last) {
			$html .= ' last';
		}
		$html.= '>'."\n";
		$html.= '<a href="'.$this->getCategoryUrl($category).'"><span>'.$this->htmlEscape($category->getName()).'</span></a>'."\n";
		static $count = 0;
		if (sizeof($activeChildren) > 0) {
			$html .= $this->drawColumns($activeChildren,$count);
		}
		$html .= "</li>";
		$count++;
		return $html;
	}

	/**
	 * Responsible for splitting the drop down box into columns and rendering the nested menus
	 *
	 * @param unknown_type $children
	 * @return unknown
	 */
	public function drawColumns($children,$menunum) {
		try  {
			$columns = $this->getConfigData("explodedmenu", "columns", "num_columns");
			if ($columns == 0) {
				$columns = 2;
			}
		} catch (Exception $ex) {
			$columns = 2;
		}
		
		$dropdownWidth = $columns * 10;		

		$html = '';
		$chunks = $this->arrayChunkVertical($children, $columns);
		$html .= "<ul class='submenu'>";
		$i = 0;
		$chuncksize = sizeof($chunks);
		$chunkref = 1;
		foreach ($chunks as $key=>$value) {
			if($chunkref == $chuncksize){
				$html .='<li class="col noborder">';
			}else{
				$html .= '<li class="col">';
			}
			$html .= $this->drawNestedMenus($value, 1, $menunum);
			$html .= '</li>';
			$i++;
			$chunkref++;
		}
		$html .='<li class="aside">
					<p>Having trouble finding a new vacuum cleaner?
                    <b>Use our vacfinder <br>or <a href="#">find a store</a></b></p>
                    <ul>
                 		<li class="i1">
                        	<a href="#" class="product-image" id="product-image-'.$menunum.'"><img name="category_image_'.$menunum.'" id="category_image_'.$menunum.'" src="" alt=""></a>
                            <a href="#" class="product-link" name="sub_category_name_'.$menunum.'" id="sub_category_name_'.$menunum.'">Bagless vacuum</a>
                            <small class="product-desc">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</small> 
                        </li>
                 	</ul></li>';
		//$html .='<li class="col"><img name="category_image" id="category_image" src="" alt="Angry face" /></li>';
		$html .= '<li class="subnav-footer">'.$this->getLayout()->createBlock('cms/block')->setBlockId('header_small_image')->toHtml().'</li>';
		$html .= '</ul>';
		
		return $html;
	}

	public function drawNestedMenus($children, $level=1, $menunum) {	
		
		$html ='<dl>';
		foreach ($children as $child) {
			
			if ($child->getIsActive()) {
				if($level == 2){
					$html .= '<dd><a href="'.$this->getCategoryUrl($child).'" onmouseover="changeImage(\''.$this->getImageUrl($child).'\',\''.$child->getName().'\',\''.$menunum.'\');changeUrl(\''.$this->getCategoryUrl($child).'\',\''.$menunum.'\',\''.$this->getImageUrl($child).'\')"><span>'.$this->htmlEscape($child->getName()).'</span></a></dd>';
				}
				else{
					if(sizeof($this->getActiveChildren($child))>0){
						$html .= '<dt><a><span>'.$this->htmlEscape($child->getName()).'</span></a></dt>';
					}
					else{
						$html .= '<dt><a href="'.$this->getCategoryUrl($child).'" onmouseover="changeInfo(\''.$menunum.'\',\''. sizeof($this->getActiveChildren($child)).'\')"><span>'.$this->htmlEscape($child->getName()).'</span></a></dt>';
					}
				}
				$activeChildren = $this->getActiveChildren($child);
				if (sizeof($activeChildren) > 0) {
					$html .= $this->drawNestedMenus($activeChildren, $level+1,$menunum );
				}
			}
		}
		$html .= '</dl>';
		return $html;
	}

	/**
	 * Gets all the active children of a category and puts them into an array. N.B. 
	 * we need an array because of the array_chunk() call in drawColumns();
	 *
	 * @param Category $parent
	 * @return unknown
	 */
	protected function getActiveChildren($parent) {
		$activeChildren = array();
		if (Mage::helper('catalog/category_flat')->isEnabled()) {
			$children = $parent->getChildrenNodes();
			$childrenCount = count($children);
		} else {
			$children = $parent->getChildren();
			$childrenCount = $children->count();
		} 
		$hasChildren = $children && $childrenCount;
		if ($hasChildren) {
			foreach ($children as $child) {
				if ($child->getIsActive()) {
					array_push($activeChildren, $child);
				}
			} 
		}
		return $activeChildren;
	}

    /**
     * Get url for category data
     *
     * @param Mage_Catalog_Model_Category $category
     * @return string
     */
    public function getCategoryPath($category)
    {        
		$url = '';
		if ($category instanceof Mage_Catalog_Model_Category) {
        $url = $category->getPathInStore();
	    $url = strtr($url, ".", "-");
	    $url = strtr($url, "/", "-");
        } else {
			// do nothing
        }
        return $url;
    }
    
    public function isDrawHomeLink() {
    	$drawHome = $this->getConfigData("explodedmenu", "home_link", "enable");
    	return $drawHome;
    }
    
	/**
	 * Returns a config value
	 *
	 * @param String $namespace e.g. 'explodedmenu' or 'supermenu'
	 * @param String $parentKey the parent key e.g. 'exploded_menu'
	 * @param String $key e.g. 'show_third_level;
	 * @return mixed
	 * @throws Exception if key not set
	 */
	private function getConfigData($namespace, $parentKey, $key) {
		$config = Mage::getStoreConfig($namespace);
		if (isset($config[$parentKey]) && isset($config[$parentKey][$key]) && strlen($config[$parentKey][$key]) > 0) {
			$value = $config[$parentKey][$key];
			return $value;
		} else {
			return false;
		}
	}  

	private function arrayChunkVertical($input, $num, $preserve_keys = FALSE) {
	    $count = count($input) ;
	    if($count)
	        $input = array_chunk($input, ceil($count/$num), $preserve_keys) ;
	    $input = array_pad($input, $num, array()) ;
	    return $input ;
	}	
	
	public function getImageUrl($category){
	
        $url = false;
        if ($image = Mage::getModel('catalog/category')->load($category->getId())->getThumbnail()) {
            $url = Mage::getBaseUrl('media').'catalog/category/'.$image;
        }
        return $url;    
		
    }
}
