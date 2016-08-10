<?php
class Godfreys_Partsfinder_Model_Accessory extends Mage_Core_Model_Abstract
{
	const CACHE_MACHINE_TAG = 'PARTS_MACHINE';
	
	const CACHE_MACHINE_PREFIX = 'parts_machine_';
	
	const CACHE_TAG = 'PARTS_ACCESSORY';
	
	const CACHE_PREFIX = 'parts_accessory_';
	
	const CACHE_LIFETIME = 7200;
	
	private $_cache;

	private $_required_args = array(
			'filter_brand',
	);
	
	protected function _construct()
	{
		$this->_cache = Mage::app()->getCacheInstance();
	}
	
	public function loadAccessory($params) {
		$keys = array();
		foreach($this->_required_args as $arg) {
			if (!isset($params[$arg])) {
				//Mage::log($arg .' is not provided');
				return false;
			}
			$keys[] = $params[$arg];
		}

		if (isset($params['filter_model']) && !empty($params['filter_model'])) {
			$keys[] = $params['filter_model'];
		}
		
		$key = implode('_', $keys);
		//Mage::log($key);
		
		$res = $this->loadAll(false);
		if (!isset($res[$key])) {
			//key not found
			//Mage::log('key '.$key.' not found');
			return false;
		}
		
		$result = $res[$key];
		if (!is_array($result)) {
			//wrong data
			//Mage::log('wrong data');
			return false;
		}
		return $result;
	}
	
	public function loadAll($useCache=true) {
		$res = false;
		
		if ($useCache) {
			$res = $this->_cache->load(self::CACHE_PREFIX . 'ALL', true);
		}

		if (empty($res)) {
			$res = array();

			$allrelations = $this->_getAllRelations();
			
			foreach($allrelations as $arr) {
				$key = $arr['brand_id'] . '_' . $arr['model_id'];
				
				if (!isset($res[$arr['brand_id']])) {
					$res[$arr['brand_id']] = array();
				}
				
				if (!isset($res[$key])) {
					$res[$key] = array();
				}
		
				$accessoryId = $arr['accessory_id'];
		
				$res[$key][] = $accessoryId;
				$res[$arr['brand_id']][] = $accessoryId;
			}
			
			$this->_cache->save(serialize($res), self::CACHE_PREFIX . 'ALL', array(self::CACHE_TAG), self::CACHE_LIFETIME);
		}
		else{
			$res = unserialize($res);
		}
		
		return $res;
	}

	public function loadAllMachine($useCache=true) {
		$machines = false;
		
		if ($useCache) {
			$machines = $this->_cache->load(self::CACHE_MACHINE_PREFIX . 'ALL', true);
		}
		
		if (empty($machines)) {
			$machines = array();
		
			$brands = Mage::getModel('partsfinder/brand')->loadAll($useCache);
			$models = Mage::getModel('partsfinder/model')->loadAll($useCache);
		
			$allrelations = $this->_getAllRelations();
		
			foreach($allrelations as $arr) {
				$accessoryId = $arr['accessory_id'];
		
				if (!isset($machines[$accessoryId])) {
					$machines[$accessoryId] = array();
				}
		
				$arr['brand'] = $brands[$arr['brand_id']]['name'];
				$arr['model'] = $models[$arr['model_id']]['name'];
				
				$machines[$accessoryId][] = $arr;
			}
		
			$this->_cache->save(serialize($machines), self::CACHE_MACHINE_PREFIX . 'ALL', array(self::CACHE_MACHINE_TAG), self::CACHE_LIFETIME);
		}
		else{
			$machines = unserialize($machines);
		}

		return $machines;
	}

	public function getRelationsById($accessoryId, $useCache = true) {
		$machines = $this->loadAllMachine($useCache);

		return empty($machines) ? array() : (isset($machines[$accessoryId]) ? $machines[$accessoryId] : array());
	}
	
	private function _getAllRelations() {
		$conn = Mage::getSingleton('core/resource')->getConnection('core_write');
		
		$tableName = $conn->getTableName('partsfinder_accessory_relation');
		
		$result = $conn->query($conn->select()->from($tableName));
		
		return $result->fetchAll();
	}

	public function loadProduct($brandId, $model)
	{
		$collection = Mage::getModel('catalog/product')->getCollection()
		->addAttributeToSelect('name')
		->addAttributeToFilter('brand', $brandId)
		->addAttributeToFilter('model', $model);
	
		return $collection->fetchItem();
	}
	
	public function addRelation($accessoryId, $brandId, $modelId, $product, $productName)
	{
		if (empty($productName) && empty($product) ) {
			throw new Exception('Product name not provided.');
			return;
		}
	
		$accessoryId = intval($accessoryId);
		$brandId = intval($brandId);
		$modelId = intval($modelId);
	
		if (0 == $accessoryId || 0 == $brandId || 0 == $modelId) {
			throw new Exception('Invalid accesory product id or invalid brand or model');
			return;
		}
	
		$productId = 0;
		$sku = '';
		if (!empty($product)) {
			$productId = $product->getId();
			$sku = $product->getSku();
	
			if (empty($productName)) {
				$productName = $product->getName();
			}
		}
	
		$conn = Mage::getSingleton('core/resource')->getConnection('core_write');
	
		$sql = "INSERT INTO {$conn->getTableName('partsfinder_accessory_relation')} (`accessory_id`, `brand_id`, `model_id`, `product_id`, `name`, `sku`) "
		."VALUES(?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `product_id`=VALUES(`product_id`), `name`=VALUES(`name`), `sku`=VALUES(`sku`)";
	
		$conn->query($sql, array($accessoryId, $brandId, $modelId, $productId, $productName, $sku));
	}
	
	public function removeRelation($accessoryId, $brandId, $modelId)
	{
		$conn = Mage::getSingleton('core/resource')->getConnection('core_write');
	
		$sql = "DELETE FROM {$conn->getTableName('partsfinder_accessory_relation')} WHERE `accessory_id`=? AND `brand_id`=? AND `model_id`=?";
	
		$conn->query($sql, array($accessoryId, $brandId, $modelId));
	}
	
}