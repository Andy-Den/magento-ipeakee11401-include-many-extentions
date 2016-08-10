<?php
class Godfreys_Partsfinder_Model_Observer
{
	public function addAccessoryFilterToCollection(Varien_Event_Observer $observer)
	{
		if (Mage::app()->getStore()->isAdmin()) {
			return;
		}
	
		$brandId = Mage::app()->getCookie()->get('brand_id');
		$modelId = Mage::app()->getCookie()->get('model_id');
	
		if (empty($brandId)) {
			return;
		}
Mage::log('brandid='.$brandId.',modelid='.$modelId);	
		$accessories = Mage::getModel('partsfinder/accessory')->loadAccessory(array('filter_brand' => $brandId, 'filter_model'=> $modelId));
		
		$collection = $observer->getEvent()->getCollection();
		
		if (!empty($accessories)) {
			//$collection->addIdFilter($accessories);
		}
	}

	public function setWarrantyLink(Varien_Event_Observer $observer)
	{
		if (!Mage::app()->getStore()->isAdmin()) return;
		
		$product = $observer->getEvent()->getProduct();
		$links = $observer->getEvent()->getRequest()->getPost('links');

		if (isset($links['custom']) && !$product->getCustomReadonly()) {
			$product->setCustomLinkData(Mage::helper('adminhtml/js')->decodeGridSerializedInput($links['custom']));
		}
		Mage::log($product->getData());
	}
}