<?php

class CJM_ColorSelectorPlus_Block_Listswatch extends Mage_Core_Block_Template
{
	protected $swatch_attributes = array();
	
	protected $attributes = array();
	
	protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('colorselectorplus/listswatches.phtml');
        if(Mage::app()->useCache('swatch')):
        	$this->addData(array(
        		'cache_lifetime'	=> 9999999999
        	));
        else:
        	$this->addData(array(
        		'cache_lifetime'	=> null
        	));
        endif;
    }
    
    public function getCacheTags()
    {
       return array(Mage_Catalog_Model_Product::CACHE_TAG . "_" . $this->getProduct()->getId(), 'swatch');
    }

    public function getCacheKey()
    {
        return 'SWATCH' . $this->getProduct()->getId();
    }
    
    protected function _toHtml()
    {
    	return parent::_toHtml();
    }
    
    public function decodeImagesForList($productId)
	{
		$_product = Mage::getModel('catalog/product')->load($productId);
		$_gallery = $_product->getMediaGalleryImages();
		$imgcount = count($_gallery);
		$product_base = array();
		$theSizes = Mage::helper('colorselectorplus')->getImageSizes();

		if($imgcount > 1){
			foreach ($_gallery as $_image )
 			{
 				$product_base['color'][] = $_image['selectorbase'];
				$product_base['image'][] = strval(Mage::helper('catalog/image')->init($_product, 'base', $_image->getFile())->resize($theSizes['list']['width'],$theSizes['list']['height']));
			}
		}
		return $product_base;	
	}
	
	public function getListSwatchAttributes()
	{
		$store = Mage::app()->getStore();
	
		if (isset($this->swatch_attributes[$store->getId()])) {
			return $this->swatch_attributes[$store->getId()];
		}
	
		$swatch_attribute_ids = array();
		$swatchattributes = Mage::getStoreConfig('color_selector_plus/colorselectorplusgeneral/toshow',$store);
		$swatch_attribute_ids = explode(",", $swatchattributes);
	
		$swatch_attributes = array();
	
		//load all simple attributes and match with config
		$attributes = Mage::getModel('catalog/product')->setTypeId('simple')->getAttributes();
	
		foreach($attributes as $attribute) {
			if (in_array($attribute->getId(), $swatch_attribute_ids)) {
				$this->attributes[$attribute->getAttributeCode()] = $attribute;
				$swatch_attributes[] = $attribute->getAttributeCode();
			}
		}
	
		$this->swatch_attributes[$store->getId()] = $swatch_attributes;
	
		return $swatch_attributes;
	}
	
    public function getListSwatchHtml($_attributes, $productId)
    {
		$html = '';
		$swatch_attributes = $this->getListSwatchAttributes();

		$_product = Mage::getModel('catalog/product')->load($productId);
		$swatchsize = Mage::helper('colorselectorplus/data')->getSwatchSize('list');
		$sizes = explode("x", $swatchsize);
		$width = $sizes[0];
		$height = $sizes[1];
		
		foreach($_attributes as $_attribute):
			
			if(in_array($_attribute['attribute_code'], $swatch_attributes)):
				
				$_option_vals = array();
				$attName = $_attribute['label'];  
        		$attrid = $_attribute['attribute_id'];
        		if (isset($this->attributes[$_attribute['attribute_code']])) {
        			$attributed = $this->attributes[$_attribute['attribute_code']];
        		}
        		else{
        			$attributed = Mage::getModel('eav/config')->getAttribute('catalog_product', $_attribute['attribute_code']);
        			$this->attributes[$_attribute['attribute_code']] = $attributed;
        		}
				
				foreach($attributed->getSource()->getAllOptions(true, true) as $option){
					$_option_vals[$option['value']] = array( 'internal_label' => $option['label'] );
				}
 				
 				$html .= '<span class="swatchLabel-category">'.$attName.':</span><p class="float-clearer"></p>';
 				//$html .= '<div class="swatch-category-container" style="clear:both;" id="ul-attribute'.$attrid.'-'.$productId.'">';
 				$html .= '<div class="swatch-category-container '.$_attribute['attribute_code'].'" style="clear:both;" id="ul-attribute'.$attrid.'-'.$productId.'">';
 				$html .= '<form><input type="hidden" id="hidden-the-'.$productId.'" value="'.$_attribute['attribute_code'].'" /></form>';
						
				foreach($_attribute['values'] as $value):
        			
        			$theId = $value['value_index'];
					$altText = $value['store_label'];
					$adminLabel = $_option_vals[$value['value_index']]['internal_label'];
			
					preg_match_all('/((#?[A-Za-z0-9]+))/', $adminLabel, $matches);
				
					if (count($matches[0]) > 0):
						
						$color_value = $matches[1][count($matches[0])-1];
						$findme = '#';
						$pos = strpos($color_value, $findme);
								
						$product_base = $this->decodeImagesForList($productId);			
						$product_image = Mage::helper('colorselectorplus')->findColorImage($theId,$product_base,'color', 'image');//returns url for base image
				
						if ($_product->getCjm_useimages() == 1 && $product_image):
							$html = $html.'<img src="'.$product_image.'" id="a'.$attrid.'-'.$theId.'-'.$productId.'" class="swatch-category" alt="'.$altText.'" width="'.$width.'px" height="'.$height.'px" title="'.$altText.'" ';
							$html = $html.'onclick="listSwitcher';
							$html = $html."(this,'".$productId."','".$product_image."','".$attrid."')";
							$html = $html.'" />';
						elseif (Mage::helper('colorselectorplus')->getSwatchUrl($theId)):
							$swatchimage = Mage::helper('colorselectorplus')->getSwatchUrl($theId);
							$html .= '<img onclick="listSwitcher(';
							$html .= "this,'".$productId."','".$product_image."','".$attrid."');";
							$html .= '" src="'.$swatchimage.'" id="a'.$attrid.'-'.$theId.'-'.$productId.'" class="swatch-category" alt="'.$altText.'" width="'.$width.'px" height="'.$height.'px" title="'.$altText.'" />';
						elseif($pos !== false):
							$html .= '<div onclick="listSwitcher(';
							$html .= "this,'".$productId."','".$product_image."','".$attrid."');";
							$html .= '" id="a'.$attrid.'-'.$theId.'-'.$productId.'" class="swatch-category" style="background-color:'.$color_value.'; width:'.$width.'px; height:'.$height.'px;" title="'.$altText.'">';
							$html .= '</div>';
						else:
							$swatchimage = Mage::helper('colorselectorplus')->getSwatchUrl('empty');
							$html .= '<img src="'.$swatchimage.'" id="a'.$theId.'" class="swatch-category" alt="'.$altText.'" width="'.$width.'px" height="'.$height.'px" title="'.$altText.'" />';
						endif;
					
					endif;
 				
 				endforeach;
 					
 				$html .= '</div><p class="float-clearer"></p>';
 			
 			endif;
 		
 		endforeach;
		 						
		return $html;
	}
}