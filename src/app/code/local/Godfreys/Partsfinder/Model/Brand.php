<?php
class Godfreys_Partsfinder_Model_Brand extends Mage_Core_Model_Abstract {
	
	const CACHE_TAG = 'PARTS_BRAND_';
	
	const CACHE_PREFIX = 'parts_brand_';
	
	const CACHE_LIFETIME = 7200;
	
	private $_cache;
	
	protected function _construct()
	{
		$this->_init('partsfinder/brand');
	
		$this->_cache = Mage::app()->getCacheInstance();
	}
	
	public function loadAll($useCache=true)
	{
		$res = false;
		
		if ($useCache) {
			$res = $this->_cache->load(self::CACHE_PREFIX . 'ALL', true);
		}
	
		if (empty($res))
		{
			$res = array();
	
			//GOD-1789, sort brand name for partsfinder by brand names alphabetically
			$collection = $this->getCollection()
				->addOrder('brand_name', Varien_Data_Collection::SORT_ORDER_ASC)
				->addOrder('sort_order', Varien_Data_Collection::SORT_ORDER_ASC)
				;
	
			foreach($collection as $brand) {
				if (0 == $brand->getId()) continue;
	
				$res[$brand->getId().''] = array('id' => $brand->getId(), 'name' => $brand->getBrandName(), 'option_id' => $brand->getOptionId());
			}
	
			$this->_cache->save(serialize($res), self::CACHE_PREFIX . 'ALL', array(self::CACHE_TAG), self::CACHE_LIFETIME);
		}
		else{
			$res = unserialize($res);
		}
	
		return $res;
	}
	
	public function loadByName($brandName, $useCache=true) {
		
		$brandName = trim($brandName);
		if (empty($brandName)) return false;
		
		$brands = $this->loadAll($useCache);
		
		if (empty($brands) || !is_array($brands)) return false;
		
		foreach($brands as $arr) {
			if ($arr['name'] == $brandName) {
				return $arr;
			}
		}
		
		return false;
	}
	
	public function loadById($brandId, $useCache=true) {
		$brandId = intval($brandId);
		
		if (empty($brandId)) return false;
		
		$brands = $this->loadAll($useCache);
		
		if (empty($brands) || !is_array($brands)) return false;
		
		if (isset($brands[$brandId])) return $brands[$brandId];
		
		return false;
	}
	
	public function loadBrand($brand, $toCreate=false) {
		$arr = $this->loadByName($brand, false);
		
		if (empty($arr) && !$toCreate) return false;
		
		if (isset($arr['id'])) return intval($arr['id']);
		
		$brandId = false;
		
		$attribute = Mage::getSingleton('eav/config')->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'brand');
		
		$options = $attribute->getSource()->getAllOptions(false, true);
		
		$optionId = 0;
		foreach($options as $option) {
			if ($brand == $option['label']) {
				$optionId = $option['value'];
				break;
			}
		}
		
		$conn = Mage::getSingleton('core/resource')->getConnection('core_write');
		
		if (0 == $optionId ){
			//add new option and got new option id
			$optionTable = $conn->getTableName('eav_attribute_option');
			$optionValueTable = $conn->getTableName('eav_attribute_option_value');
		
			$conn->insert($optionTable, array('attribute_id' => $attribute->getId(), 'sort_order'=> 0));
			$optionId = $conn->lastInsertId($optionTable);
		
			$conn->insert($optionValueTable, array('option_id' => $optionId, 'store_id' => 0, 'value' => $brand));
		}
		
		//add new brand with option id and return new brand id
		if (0 !=  $optionId) {
			$brandTable = $conn->getTableName('partsfinder_brand');
			$conn->insert($brandTable, array('brand_name' => $brand, 'option_id' => $optionId, 'sort_order' => 0));
		
			$brandId = $conn->lastInsertId($brandTable);
		}
		
		return $brandId;
	}
}