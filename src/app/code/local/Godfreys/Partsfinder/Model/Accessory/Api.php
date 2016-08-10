<?php
class Godfreys_Partsfinder_Model_Accessory_Api extends Mage_Api_Model_Resource_Abstract
{
	public function upsert($accessoryId, $brand, $model, $productName)
	{
		//validate parameters, $accessoryId >0 , $brand not empty, $model not empty.
		$accessoryId = intval($accessoryId);

		if (0==$accessoryId || empty($brand) || empty($model)) {
			$this->_fault('data_invalid');
			return false;
		}

		try{
			// load brand, to add if not found
			$brandId = $this->_loadBrand($brand, true);

			// load model, to add if not found
			$modelId = $this->_loadModel($model, $brandId, true);

			// load product_id and sku of machine for specified brand and model
			// if productName is empty, try to use product name found by brand and model,
			// throw error while still not found
			$product = $this->_loadProduct($brandId, $model);

			//INSERT INTO partsfinder_accessory_relation ON DUPLICATED KEY REPLACE
			$this->_addRelation($accessoryId, $brandId, $modelId, $product, $productName);

			return true;

		}
		catch(Exception $e)
		{
			$this->_fault('data_invalid', $e->getMessage());
			return false;
		}
	}

	public function remove($accessoryId, $brand, $model)
	{
		//validate parameters
		$accessoryId = intval($accessoryId);

		if (0 == $accessoryId || empty($brand) || empty($model)) {
			$this->_fault('data_invalid');
			return false;
		}

		//remove relationship only
		try{
			$brandId = $this->_loadBrand($brand);
				
			if (empty($brandId)) {
				$this->_fault('data_invalid', 'Brand ' . $brand . ' not found');
				return false;
			}
				
			$modelId = $this->_loadModel($model, $brandId);
				
			if (empty($modelId)) {
				$this->_fault('data_invalid', 'Model ' . $model . ' not found');
				return false;
			}

			$this->_removeRelation($accessoryId, $brandId, $modelId);
				
			return true;
		}
		catch(Exception $e)
		{
			$this->_fault('data_invalid', $e->getMessage());
			return false;
		}
	}
	
	public function getlist($accessoryId) {
		$accessoryId = intval($accessoryId);
		
		if (0 == $accessoryId ) {
			$this->_fault('data_invalid');
			return false;
		}
		
		try{
			$relations = Mage::getModel('partsfinder/accessory')->getRelationsById($accessoryId, false);
			return $relations;
		}
		catch(Exception $e) 
		{
			$this->_fault('data_invalid', $e->getMessage());
			return false;
		}
	}
	
	protected function _loadBrand($brand, $toCreate=false)
	{
		return Mage::getSingleton('partsfinder/brand')->loadBrand($brand, $toCreate);
	}
	
	protected function _loadModel($model, $brandId, $toCreate=false)
	{
		return Mage::getSingleton('partsfinder/model')->loadModel($model, $brandId, $toCreate);
	}
	
	protected function _loadProduct($brandId, $model)
	{
		return Mage::getSingleton('partsfinder/accessory')->loadProduct($brandId, $model);
	}
	
	protected function _addRelation($accessoryId, $brandId, $modelId, $product, $productName)
	{
		Mage::getSingleton('partsfinder/accessory')->addRelation($accessoryId, $brandId, $modelId, $product, $productName);
	}
	
	protected function _removeRelation($accessoryId, $brandId, $modelId)
	{
		Mage::getSingleton('partsfinder/accessory')->removeRelation($accessoryId, $brandId, $modelId);
	}
}