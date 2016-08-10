<?php
/**
 * Vacspare_Optionimages Extension
 *
 * @category    Local
 * @package     Vacspare_Optionimages
 * @author      dungnv (dungnv@arrowhitech.com)
 * @copyright   Copyright(c) 2011 Arrowhitech Inc. (http://www.arrowhitech.com)
 *
 */

/**
 *
 * @category   Local
 * @package    Vacspare_Optionimages
 * @author     dungnv <dungnv@arrowhitech.com>
 */
class Vacspare_Optionimages_Block_View extends Mage_Catalog_Block_Layer_View
{
     /**
     * Get all layer filters
     *
     * @return array
     */
    public function getFilters()
    {
        $filters = array();
        if ($categoryFilter = $this->_getCategoryFilter()) {
            $filters[] = $categoryFilter;
        }
		

        $filterableAttributes = $this->_getFilterableAttributes();
        foreach ($filterableAttributes as $attribute) {
			if(strstr($attribute->getAttributeCode(),'color_')) {
				$filters['color'][] = $this->getChild($attribute->getAttributeCode().'_filter');	
			}
			else {
				$filters[] = $this->getChild($attribute->getAttributeCode().'_filter');
			}
        }
		
        return $filters;
    }
    
    public function canShowOptions()
    {
        foreach ($this->getFilters() as $filter) {
            if (!is_array($filter) && $filter->getItemsCount()) {
                return true;
            }
        }
        return false;
    }	
}
