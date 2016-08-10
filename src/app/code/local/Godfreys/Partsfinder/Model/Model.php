<?php
class Godfreys_Partsfinder_Model_Model extends Mage_Core_Model_Abstract {
	const CACHE_TAG = 'PARTS_MODEL_';
	
	const CACHE_PREFIX = 'parts_model_';
	
	const CACHE_LIFETIME = 7200;
	
	private $_cache;
	
	protected function _construct()
	{
		$this->_init('partsfinder/model');
		
		$this->_cache = Mage::app()->getCacheInstance();
	}
		
	public function getModelsByBrandId($brandId, $useCache=true) {
		$res = false;
		
		if ($useCache) {
			$res = $this->_cache->load(self::CACHE_PREFIX. $brandId, true);
		}
	
		if (empty($res)) {
			$res = array();
	
			$all = $this->loadAll();
	
			if (empty($all)) return false;
			
			if (!is_array($all)) Mage::log($all);
			
			foreach($all as $id => $m) {
				if ($m['brand_id'] != $brandId) continue;

				$res[] = $m;
			}
	
			if (!empty($res)) {
				$this->_cache->save(serialize($res), self::CACHE_PREFIX. $brandId, array(self::CACHE_TAG), self::CACHE_LIFETIME);
			}
		}
		else{
			$res = unserialize($res);
		}
		return empty($res) ? false : $res;
	}
	
	public function loadAll($useCache=true) {
		$res = false;
		
		if ($useCache) {
			$res = $this->_cache->load(self::CACHE_PREFIX . 'ALL', true);
		}
	
		if (empty($res))
		{
			$res = array();
	
			$brands = Mage::getModel('partsfinder/brand')->loadAll($useCache);
	
			$collection = $this->getCollection()
				->addOrder('brand_id', Varien_Data_Collection::SORT_ORDER_ASC)
				->addOrder('model_name', Varien_Data_Collection::SORT_ORDER_ASC);
			
	
			foreach($collection as $model) {
				if (0 == $model->getId()) continue;
				
				if (!isset($brands[$model->getBrandId()])) continue;
	
				$res[$model->getId()] = array(
						'id' => $model->getId(), 
						'name' => $model->getModelName(), 
						'brand_id' => $model->getBrandId(), 
						'option_id' => $brands[$model->getBrandId()]['option_id']
					);
			}
	
			$this->_cache->save(serialize($res), self::CACHE_PREFIX . 'ALL', array(self::CACHE_TAG), self::CACHE_LIFETIME);
		}
		else{
			$res = unserialize($res);
		}
	
		return $res;
	}
	
	public function loadModel($model, $brandId, $toCreate=false)
	{
		$models = $this->getModelsByBrandId($brandId, false);
	
		if (empty($models) && !$toCreate) return false;
	
		if (!empty($models)) {
			foreach($models as $m) {
				if ($model == $m['name']) return $m['id'];
			}
			if (!$toCreate) return false;
		}
	
		//add new model and return new model id
		$modelId = false;
	
		$conn = Mage::getSingleton('core/resource')->getConnection('core_write');
	
		$modelTable = $conn->getTableName('partsfinder_model');
	
		$conn->insert($modelTable, array('model_name' => $model, 'brand_id' => $brandId));
	
		$modelId = $conn->lastInsertId($modelTable);
	
		return $modelId;
	}
	
}