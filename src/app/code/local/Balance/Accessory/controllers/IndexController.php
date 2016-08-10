<?php

class Balance_Accessory_IndexController extends Mage_Core_Controller_Front_Action {
	
	
	public function indexAction()
	{
		echo Mage::app()->getStore()->getStoreId() . ":<br />";
		var_dump(Mage::getStoreConfig(Balance_Accessory_Helper_Data::XML_PATH_CATEGORY_ID, Mage::app()->getStore()->getStoreId()));
		var_dump(Mage::getStoreConfig(Balance_Accessory_Helper_Data::XML_PATH_CATEGORY_ID, 2));
	}
	
}