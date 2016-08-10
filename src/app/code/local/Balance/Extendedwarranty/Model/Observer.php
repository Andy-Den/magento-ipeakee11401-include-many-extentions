<?php



class Balance_Extendedwarranty_Model_Observer
{
	public function __construct()
	{
		
	}
	/**
	* 
	* @param   Varien_Event_Observer $observer
	*/
	public function addExtendedWarrantyItem($observer)
	{
		if(Mage::getStoreConfig('extendedwarranty/settings/enabled'))
		{
			$productId = $observer->getControllerAction()->getRequest()->getParam('product');
			$options = $observer->getControllerAction()->getRequest()->getParam('options');
			Mage::log('observer fired!' , null , "Balance_Extendedwarranty.log");
			Mage::log('Product Id: ' . $productId , null , "Balance_Extendedwarranty.log");
			Mage::log('Options: ' . print_r($options,true) , null , "Balance_Extendedwarranty.log");
			
			//Check if Extended Warranty Option chosen
			
			//Lookup Extended Warranty SKU
			
			//Add Extended Warranty SKU to cart
			
			//Remove Extended Warranty option from product
			
		}
	}
	

}
