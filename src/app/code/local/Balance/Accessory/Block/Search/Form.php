<?php
/**
 * The mini search form that appears around the site
 */
class Balance_Accessory_Block_Search_Form extends Balance_Accessory_Block_Search_Form_Abstract {
    
    /**
     * Retrieve collection of accessory searchable attributes
     *
     * @return Varien_Data_Collection_Db
     */
    public function getSearchAttributes()
    {
        $attributes = $this->getModel()->getFullSearchAttributes();
        return $attributes;
    }
    
    
}