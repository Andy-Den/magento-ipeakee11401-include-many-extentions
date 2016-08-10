<?php
/**
 * The mini search form that appears around the site
 */
class AW_Mobile_Block_Catalog_Category_View extends AW_Mobile_Block_Catalog_Category_View_Abstract {
    
    /**
     * Retrieve collection of accessory searchable attributes
     *
     * @return Varien_Data_Collection_Db
     */
    public function getSearchAttributes()
    {	
		$class = new Balance_Accessory_Model_Catalog_Product_Accessory();
        $attributes = $class->getFullSearchAttributes();
        return $attributes;
    }
	
}