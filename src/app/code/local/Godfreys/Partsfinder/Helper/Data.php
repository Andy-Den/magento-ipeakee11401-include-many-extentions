<?php
class Godfreys_Partsfinder_Helper_Data extends Mage_Core_Helper_Data
{
	public function getBrandName() 
	{
		$brand_id = Mage::app()->getRequest()->getParam('filter_brand');
		$brand = Mage::getModel('partsfinder/brand')->loadById($brand_id);
		return empty($brand) ? '' : $this->htmlEscape($brand['name']); 
	}
}
