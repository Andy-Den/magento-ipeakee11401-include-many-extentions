<?php 
class Tal_Custom_Block_Reports_Product_Viewed extends Mage_Reports_Block_Product_Viewed
{
	public function getItemsCollection()
    {        
       	$this->_collection = parent::getItemsCollection()->addAttributeToSelect('image');
            
        return $this->_collection;
    }
}