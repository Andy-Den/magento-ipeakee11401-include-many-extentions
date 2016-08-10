<?php
class Godfreys_Partsfinder_AjaxController extends Mage_Core_Controller_Front_Action
{
	public function filterbrandAction()
	{
		$brands = Mage::getModel('partsfinder/brand')->loadAll();
	
		$brands = (empty($brands) || !is_array($brands)) ? array() : array_values($brands);
		$result =  array(array('id' => 0, 'name'=>__('Please Select')));
	
		foreach($brands as $brand) {
			$result[] = $brand;
		}
	
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
	}
	
	public function filtermodelAction()
	{
		$brand_id = $this->getRequest()->getParam('filter_brand');
	
		$models = Mage::getModel('partsfinder/model')->getModelsByBrandId($brand_id);
	
		$models = (false == $models) ? array() : $models;
		$result = array(array('id' => 0, 'name'=>__('Please Select')));
	
		foreach($models as $model) {
			$result[] = $model;
		}
	
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
	}
	
}