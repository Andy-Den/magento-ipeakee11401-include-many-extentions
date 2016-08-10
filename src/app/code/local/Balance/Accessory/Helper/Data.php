<?php
/**
 * Helper
 * Enter description here ...
 * @author carey
 *
 */
class Balance_Accessory_Helper_Data extends Mage_Core_Helper_Data
{
	const XML_PATH_CATEGORY_ID = 'accessory/settings/category';
        const ATTR_CODE_SUITS_MODELS = 'suits_models';

	/**
	 * Get each of the categories that the product can be added into
	 * These categories hold the accessories on the frontend
	 * @param Mage_Catalog_Model_Product $product
	 * @return array Mage_Catalog_Model_Category
	 */
	public function getAccessoryCategories(Mage_Catalog_Model_Product $product)
	{
		$categories = array();
		foreach($product->getStoreIds() as $storeId) {
			$categoryId = Mage::getStoreConfig(self::XML_PATH_CATEGORY_ID, $storeId);
			$categories[] = Mage::getModel('catalog/category')->load($categoryId);
		}
		return $categories;
	}

	/**
	 * Get each of the accessories based on the category set in the current store's config
	 */
	public function getCurrentStoreAccessories()
	{
		$categoryId = Mage::getStoreConfig(self::XML_PATH_CATEGORY_ID, Mage::app()->getStore()->getStoreId());
		$category = Mage::getModel('catalog/category')->load($categoryId);
		$productIds = array();
		foreach($category->getProductsPosition() as $productId => $position) {
			if(!in_array($productId, $productIds)) {
				$productIds[] = $productId;
			}
		}
		$collection = Mage::getResourceModel('catalog/product_collection');
		$collection->addIdFilter($productIds);
//		$collection->addAttributeToSelect($accessoryBrandsCode);
//		$collection->addAttributeToSelect($accessoryModelsCode);
		
		
		return $collection;
	}

	/**
	 * Get a collection of products that are accessories
	 * @return Mage_Catalog_Model_Resource_Product_Collection
	 */
	public function getAllAccessories()
	{
		$categories = array();
		foreach(Mage::app()->getStores() as $store) {
			$categoryId = Mage::getStoreConfig(self::XML_PATH_CATEGORY_ID, $store->getStoreId());
			$categories[] = Mage::getModel('catalog/category')->load($categoryId);
		}
		$productIds = array();
		/* @var $category Mage_Catalog_Model_Category */
		foreach($categories as $category) {
			foreach($category->getProductsPosition() as $productId => $position) {
				if(!in_array($productId, $productIds)) {
					$productIds[] = $productId;
				}
			}
		}
		$model = Mage::getModel('accessory/catalog_product_accessory');
		$accessoryBrandsCode = $model->getAccessoryBrandsAttribute()->getAttributeCode();
		$accessoryModelsCode = $model->getAccessoryModelsAttribute()->getAttributeCode();
		
		$collection = Mage::getResourceModel('catalog/product_collection');
		$collection->addIdFilter($productIds);
		$collection->addAttributeToSelect($accessoryBrandsCode);
		$collection->addAttributeToSelect($accessoryModelsCode);
		
		return $collection;
	}
}
