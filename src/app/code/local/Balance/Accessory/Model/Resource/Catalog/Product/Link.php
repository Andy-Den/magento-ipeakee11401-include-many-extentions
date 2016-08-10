<?php

/**
 * Accessory catalog product link resource model
 *
 * @category Mage
 * @package Mage_Catalog
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Balance_Accessory_Model_Resource_Catalog_Product_Link extends Mage_Catalog_Model_Resource_Product_Link {

	/**
	 * Save Product Links process
	 *
	 * @param Mage_Catalog_Model_Product $product
	 * @param array $data
	 * @param int $typeId
	 * @return Mage_Catalog_Model_Resource_Product_Link
	 */
	public function saveProductLinks($product, $data, $typeId) {
		$storeCode = Mage::app()->getStore()->getCode();
			
		$links = Mage::getModel('catalog/product_link')
		->useAccessoryLinks()
		->getCollection()
		->addFieldToFilter('link_type_id', array('eq' => Balance_Accessory_Model_Catalog_Product_Link::LINK_TYPE_ACCESSORY))
		->addFieldToFilter('product_id', array('eq' => $product->getId()));
		$oldLinkedProductIds = array();
		foreach ($links as $link) {
			if (!in_array($link->getLinkedProductId(), $oldLinkedProductIds)) {
				$oldLinkedProductIds[] = $link->getLinkedProductId();
			}
		}
		parent::saveProductLinks($product, $data, $typeId);

		if ($typeId == Balance_Accessory_Model_Catalog_Product_Link::LINK_TYPE_ACCESSORY) {
			foreach ($data as $linkedProductId => $linkInfo) {
				$this->_populateAccessoryAttributes($linkedProductId);
			}

			$this->_refreshAccessoryCategoryAssociation();

			$this->_refreshRelatedProductAssociation();
		}

		$links = Mage::getModel('catalog/product_link')
		->useAccessoryLinks()
		->getCollection()
		->addFieldToFilter('link_type_id', array('eq' => Balance_Accessory_Model_Catalog_Product_Link::LINK_TYPE_ACCESSORY))
		->addFieldToFilter('product_id', array('eq' => $product->getId()));
		$newLinkedProductIds = array();
		foreach ($links as $link) {
			if (!in_array($link->getLinkedProductId(), $newLinkedProductIds)) {
				$newLinkedProductIds[] = $link->getLinkedProductId();
			}
		}

		$deletedLinkedProductIds = array_diff($oldLinkedProductIds, $newLinkedProductIds);
		foreach ($deletedLinkedProductIds as $id) {
			$this->_deleteLink($product->getId(), $id);
		}

		return $this;
	}

	/**
	 *
	 * @param type $productId
	 * @param type $oldLinkAccessoryId
	 */
	protected function _deleteLink($productId, $oldLinkAccessoryId) {
		$accessory = Mage::getModel('catalog/product')->load($oldLinkAccessoryId);
		$product = Mage::getModel('catalog/product')->load($productId);
		$accessoryModelAttr = Mage::getStoreConfig(Balance_Accessory_Model_Catalog_Layer_Filter_Accessory_Model::XML_PATH_ATTRIBUTE_CODE);

		// clear out the model attribute
		$productModelAttr = Mage::getStoreConfig(Balance_Accessory_Model_Catalog_Product_Accessory::XML_PATH_PRODUCT_MODEL_ATTRIBUTE_CODE);
		$modelToDelete = $product->getData($productModelAttr);
		$optionId = $this->_attributeValueExists($accessoryModelAttr, $modelToDelete);
		if ($optionId) {
			$options = explode(",", $accessory->getData($accessoryModelAttr));
			if (!is_array($options)) {
				$options = array($options);
			}
			$newOptions = array();
			foreach ($options as $option) {
				if ($option != $optionId) {
					$newOptions[] = $option;
				}
			}
			$accessory->setData($accessoryModelAttr, implode(",", $newOptions));
			$accessory->save();
		}

		// clear out the brand attribute on the accessory if needed
		$productBrandAttr = Mage::getStoreConfig(Balance_Accessory_Model_Catalog_Product_Accessory::XML_PATH_PRODUCT_BRAND_ATTRIBUTE_CODE);
		$accessoryBrandAttr = Mage::getStoreConfig(Balance_Accessory_Model_Catalog_Layer_Filter_Accessory_Brand::XML_PATH_ATTRIBUTE_CODE);
		$selectedBrands = explode(",", $accessory->getData($accessoryBrandAttr));
		$brandIdsToDelete = array();
		foreach ($selectedBrands as $brandId) {
			$productBrandValue = $this->_getAttributeValue($accessoryBrandAttr, $brandId);
			$links = Mage::getModel('catalog/product_link')
			->useRelatedLinks()
			->getCollection()
			->addFieldToFilter('link_type_id', array('eq' => Mage_Catalog_Model_Product_Link::LINK_TYPE_RELATED))
			->addFieldToFilter('product_id', array('eq' => $accessory->getId()));
			$keepValue = false;
			foreach ($links as $link) {
				$related = Mage::getModel('catalog/product')->load($link->getLinkedProductId());
				if ($this->_getAttributeValue($productBrandAttr, $related->getData($productBrandAttr)) == $productBrandValue) {
					$keepValue = true;
				}
			}
			// delete the brand from the accessory's available brands multiselect
			if (!$keepValue) {
				$brandIdsToDelete[] = $brandId;
			}
		}
		$cleanedBrands = array();
		foreach ($selectedBrands as $brand) {
			if (!in_array($brand, $brandIdsToDelete)) {
				$cleanedBrands[] = $brand;
			}
		}
		$accessory->setData($accessoryBrandAttr, implode(",", $cleanedBrands));
		$accessory->save();
	}

	/**
	 * Get the accessory product ids
	 * @return array int
	 */
	protected function _getAccessoryProductIds() {
		$links = Mage::getModel('catalog/product_link')
		->useAccessoryLinks()
		->getCollection()
		->addFieldToFilter('link_type_id', array('eq' => Balance_Accessory_Model_Catalog_Product_Link::LINK_TYPE_ACCESSORY));
		$links->getSelect()->group('linked_product_id');
		$links->load();
		$accessoryIds = array();
		foreach ($links as $link) {
			$accessoryIds[] = $link->getLinkedProductId();
		}
		return $accessoryIds;
	}

	/**
	 * Refresh all of the related product links for each accessory
	 */
	protected function _refreshRelatedProductAssociation() {
		$accessoryIds = $this->_getAccessoryProductIds();
		foreach ($accessoryIds as $accessoryId) {
			// links to accessory is all the products that reference this accessory
			// as an accessory
			$linksToAccessory = Mage::getModel('catalog/product_link')
			->useAccessoryLinks()
			->getCollection()
			->addFieldToFilter('link_type_id', array('eq' => Balance_Accessory_Model_Catalog_Product_Link::LINK_TYPE_ACCESSORY))
			->addFieldToFilter('linked_product_id', array('eq' => $accessoryId));
			$linksToAccessory->getSelect()->group('product_id');
			$linksToAccessory->load();

			foreach ($linksToAccessory as $linkToAccessory) {
				$relatedLinksOnAccessory = Mage::getModel('catalog/product_link')
				->useRelatedLinks()
				->getCollection()
				->addFieldToFilter('link_type_id', array('eq' => Mage_Catalog_Model_Product_Link::LINK_TYPE_RELATED))
				->addFieldToFilter('product_id', array('eq' => $accessoryId));
				$relatedLinksOnAccessory->getSelect()->group('linked_product_id');
				$relatedLinksOnAccessory->load();
				$alreadyRelatedOnAccessory = false;
				foreach ($relatedLinksOnAccessory as $relatedLinkOnAccessory) {

					if ($relatedLinkOnAccessory->getLinkedProductId() == $linkToAccessory->getProductId()) {
						$alreadyRelatedOnAccessory = true;
						break;
					}
				}
				if (!$alreadyRelatedOnAccessory) {
					// create the related link
					// from the accessory
					// to the product
					Mage::getModel('catalog/product_link')
					->setProductId($accessoryId)
					->setLinkedProductId($linkToAccessory->getProductId())
					->setLinkTypeId(Mage_Catalog_Model_Product_Link::LINK_TYPE_RELATED)
					->save();
				}
			}
		}

		// now check if there are any that need to be removed
		$accessoryIds = $this->_getAccessoryProductIds();
		foreach ($accessoryIds as $accessoryId) {
			$relatedLinksOnAccessory = Mage::getModel('catalog/product_link')
			->useRelatedLinks()
			->getCollection()
			->addFieldToFilter('link_type_id', array('eq' => Mage_Catalog_Model_Product_Link::LINK_TYPE_RELATED))
			->addFieldToFilter('product_id', array('eq' => $accessoryId));
			$relatedLinksOnAccessory->getSelect()->group('linked_product_id');
			$relatedLinksOnAccessory->load();
			foreach ($relatedLinksOnAccessory as $relatedLinkOnAccessory) {
				$accessoryLinksOnRelated = Mage::getModel('catalog/product_link')
				->useAccessoryLinks()
				->getCollection()
				->addFieldToFilter('link_type_id', array('eq' => Balance_Accessory_Model_Catalog_Product_Link::LINK_TYPE_ACCESSORY))
				->addFieldToFilter('product_id', array('eq' => $relatedLinkOnAccessory->getLinkedProductId()));
				$hasFoundAccessoryId = false;
				foreach($accessoryLinksOnRelated as $accessoryLinkOnRelated) {
					if($accessoryLinkOnRelated->getLinkedProductId() == $accessoryId) {
						$hasFoundAccessoryId = true;
						break;
					}
				}
				if(!$hasFoundAccessoryId) {
					$relatedLinkOnAccessory->delete();
				}
			}

		}
	}

	/**
	 * Refresh category association for accessories
	 * @param Mage_Catalog_Model_Product $product
	 * @param type $data
	 */
	protected function _refreshAccessoryCategoryAssociation() {
		$accessoryIds = $this->_getAccessoryProductIds();
		$category = Mage::getModel('accessory/catalog_product_accessory')->getCategory();
		$positions = $category->getProductsPosition();

		foreach ($accessoryIds as $accessoryId) {
			if (!isset($positions[$accessoryId])) {
				// it needs to be added in
				$this->_addToAccessoryCategory($accessoryId);
			}
			//$this->_populateAccessoryAttributes($accessoryId); // resolves bug where a pre-existing accessory already in category didn't get new brands and models added to it
		}
		// check for products in the category that shouldn't be
		$productCollection = Mage::helper('accessory')->getAllAccessories();
		foreach ($productCollection as $product) {
			if (!in_array($product->getId(), $accessoryIds)) {
				// it needs to be deleted
				$this->_removeFromAccessoryCategory($product->getId());
			}
		}

		// finally, check all attributes that are listed in the multiselects shouldnt be there
		// think of these two as 'brands applicable to accessory' and 'models applicable to accessory'
		$model = Mage::getModel('accessory/catalog_product_accessory');
		$accessoryBrandsCode = $model->getAccessoryBrandsAttribute()->getAttributeCode();
		$accessoryModelsCode = $model->getAccessoryModelsAttribute()->getAttributeCode();

		$usedBrands = array();
		$usedModels = array();
		$productCollection = Mage::helper('accessory')->getAllAccessories();
		foreach ($productCollection as $accessory) {
			$selectedBrands = explode(",", $accessory->getData($accessoryBrandsCode));
			foreach ($selectedBrands as $brand) {
				if (!is_null(trim($brand)) && !in_array($brand, $usedBrands) && $brand != '') {
					$usedBrands[] = $brand;
				}
			}
			$selectedModels = explode(",", $accessory->getData($accessoryModelsCode));
			foreach ($selectedModels as $modelId) {
				if (!is_null(trim($modelId)) && !in_array($modelId, $usedModels) && $modelId != '') {
					$usedModels[] = $modelId;
				}
			}
		}
		// if it's not in used brands array, delete it
		// get the attribute model
		$accessoryBrands = $model->getAccessoryBrandsAttribute();

		foreach ($this->_getAttributeOptionIds($accessoryBrands) as $optionId) {
			if (!in_array($optionId, $usedBrands)) {
				$this->_removeMultiselectOption($optionId, $accessoryBrands);
			}
		}

		$accessoryModels = $model->getAccessoryModelsAttribute();
		foreach ($this->_getAttributeOptionIds($accessoryModels) as $optionId) {
			if (!in_array($optionId, $usedModels)) {
				$this->_removeMultiselectOption($optionId, $accessoryModels);
			}
		}
	}

	/**
	 * Remove options from the multiselect
	 * @param $values - id of option to delete
	 * @param $attribute Mage_Eav_Model_Attribute
	 */
	protected function _removeMultiselectOption($optionId, $attribute) {
		$model = Mage::getModel('catalog/resource_eav_attribute');
		$model->load($attribute->getId());
		$data = array();
		$data['attribute_id'] = $attribute->getId();
		$data['option']['value'] =
		array($optionId => array(
		$optionId => '0'
		));
		$data['option']['order'] = array(
		$optionId => '0'
		);
		$data['option']['delete'] = array(
		$optionId => '1'
		);
		$model->addData($data);
		$model->save();
	}

	/**
	 * Add product to accessory category
	 * @param int $productId
	 */
	protected function _addToAccessoryCategory($productId, $position = null) {
		$product = Mage::getModel('catalog/product')->load($productId);
		foreach(Mage::helper('accessory')->getAccessoryCategories($product) as $category) {
			$positions = $category->getProductsPosition();
			$positions[$productId] = $position;
			$category->setPostedProducts($positions);
			try {
				$category->save();
			}
			catch (Mage_Core_Exception $e) {
				Mage::logException($e);
			}
		}
	}

	/**
	 * Remove the product from the accessory category
	 * @param int $productId
	 */
	protected function _removeFromAccessoryCategory($productId) {
		$product = Mage::getModel('catalog/product')->load($productId);
		foreach(Mage::helper('accessory')->getAccessoryCategories($product) as $category) {
			$positions = $category->getProductsPosition();

			if (isset($positions[$productId])) {
				unset($positions[$productId]);
				$category->setPostedProducts($positions);
			}
			try {
				$category->save();
			}
			catch (Mage_Core_Exception $e) {
				Mage::logException($e);
			}
		}
	}

	/**
	 * Populate the Model and Brand attributes on an accessory
	 * Looks through each of the products in the catalog listing
	 * the $accessory as an accessory, and takes the model and brand
	 * from those products
	 *
	 * @param Mage_Catalog_Model_Product id $accessory
	 */
	protected function _populateAccessoryAttributes($accessoryId) {

		$model = Mage::getModel('accessory/catalog_product_accessory');

		// think of these two as the brand and model attribute held by a regular product
		$catalogBrandCode = $model->getCatalogBrandAttribute()->getAttributeCode();
		$catalogModelCode = $model->getCatalogModelAttribute()->getAttributeCode();
		// think of these two as 'brands applicable to accessory' and 'models applicable to accessory'
		$accessoryBrandsCode = $model->getAccessoryBrandsAttribute()->getAttributeCode();
		$accessoryModelsCode = $model->getAccessoryModelsAttribute()->getAttributeCode();

		// get all products that reference this accessory as an accessory
		$links = Mage::getModel('catalog/product_link')
		->useAccessoryLinks()
		->getCollection()
		->addFieldToFilter('link_type_id', array('eq' => Balance_Accessory_Model_Catalog_Product_Link::LINK_TYPE_ACCESSORY))
		->addFieldToSelect('product_id')
		->addFieldToFilter('linked_product_id', array('eq' => $accessoryId));
		$links->getSelect()->group('product_id');
		$links->load();

		$productIds = array();
		foreach ($links as $link) {
			$productIds[] = $link->getProductId();
		}
		$collection = Mage::getModel('catalog/product')
		->getCollection()
		->addAttributeToFilter('entity_id', array('in' => $productIds))
		->addAttributeToSelect($catalogBrandCode)
		->addAttributeToSelect($catalogModelCode);
		$brands = array();
		$models = array();
		foreach ($collection as $product) {
			// take the brand and model from the product and add it to the array
			if ($product->getData($catalogBrandCode)
			&& !in_array($this->_getAttributeValue($catalogBrandCode, $product->getData($catalogBrandCode)), $brands)) {

				$brands[] = $this->_getAttributeValue($catalogBrandCode, $product->getData($catalogBrandCode));
			}
			if ($product->getData($catalogModelCode)
			&& !in_array($product->getData($catalogModelCode), $models)) {
				$models[] = $product->getData($catalogModelCode); // models are only text fields
			}
		}

		$accessory = Mage::getModel('catalog/product')->load($accessoryId);
		// do the brands
		foreach ($brands as $brand) {
			if (!$this->_attributeValueExists($accessoryBrandsCode, $brand)) {
				$this->_addAttributeValue($accessoryBrandsCode, $brand);
			}
			$optionId = $this->_attributeValueExists($accessoryBrandsCode, $brand);
			if ($optionId) {

				$mergedSelections = explode(",", $accessory->getData($accessoryBrandsCode));

				if (!in_array($optionId, $mergedSelections)) {
					$mergedSelections[] = $optionId;
				}
				$accessory->addData(array($accessoryBrandsCode => implode(",", $mergedSelections)));
			}
		}
		// do the models
		foreach ($models as $model) {
			if (!$this->_attributeValueExists($accessoryModelsCode, $model)) {
				$this->_addAttributeValue($accessoryModelsCode, $model);
			}
			$optionId = $this->_attributeValueExists($accessoryModelsCode, $model);
			if ($optionId) {

				$mergedSelections = explode(",", $accessory->getData($accessoryModelsCode));
				if (!in_array($optionId, $mergedSelections)) {
					$mergedSelections[] = $optionId;
				}
				$accessory->addData(array($accessoryModelsCode => implode(",", $mergedSelections)));
			}
		}
		// and finally
		$accessory->save();
	}

	/**
	 * Add an attribute value (if it doesn't exist)
	 * @param string $attributeCode - the attribute name
	 * @param string $value - the attribute value
	 * @return boolean
	 */
	private function _addAttributeValue($argAttribute, $argValue) {
		$attributeModel = Mage::getModel('eav/entity_attribute');
		$attributeOptionsModel = Mage::getModel('eav/entity_attribute_source_table');

		$attributeCode = $attributeModel->getIdByCode('catalog_product', $argAttribute);
		$attribute = $attributeModel->load($attributeCode);

		$attributeTable = $attributeOptionsModel->setAttribute($attribute);
		$options = $attributeOptionsModel->getAllOptions(false);

		if (!$this->_attributeValueExists($argAttribute, $argValue)) {
			$value['option'] = array($argValue, $argValue);
			$result = array('value' => $value);
			$attribute->setData('option', $result);
			try {
				$attribute->save();
			}
			catch (Exception $ex) {
				throw new Exception("Error setting attribute: " . $argAttribute . " with value: " . $argValue . " for a basic product (value or attribute is probably null)");
			}
		}

		foreach ($options as $option) {
			if ($option['label'] == $argValue) {
				return $option['value'];
			}
		}
		return true;
	}

	/**
	 * Get the value for an attribute using option id
	 * @param string $argAttribute - the attribute name
	 * @param int $argOptionId - the option id
	 * @return string value
	 */
	private function _getAttributeValue($argAttribute, $argOptionId) {
		$attributeModel = Mage::getModel('eav/entity_attribute');
		$attributeTable = Mage::getModel('eav/entity_attribute_source_table');

		$attributeCode = $attributeModel->getIdByCode('catalog_product', $argAttribute);
		$attribute = $attributeModel->load($attributeCode);

		$attributeTable->setAttribute($attribute);

		$option = $attributeTable->getOptionText($argOptionId);

		return $option;
	}

	/**
	 * Get all option ids for an attribute
	 * @param Mage_Eav_Model_Attribute $attribute
	 * @return array(int)
	 */
	private function _getAttributeOptionIds(Mage_Eav_Model_Attribute $attribute) {
		$attributeOptionsModel = Mage::getModel('eav/entity_attribute_source_table');
		$attributeTable = $attributeOptionsModel->setAttribute($attribute);
		$options = $attributeOptionsModel->getAllOptions(false);
		$optionIds = array();
		foreach ($options as $option) {
			$optionIds[] = $option['value'];
		}
		return $optionIds;
	}

	/**
	 * Check if a value for an attribute exists
	 * @param string $argAttribute - the attribute name
	 * @param string $argValue - the attribute value
	 * @return false is attribute's value doesn't exist, or the attributes value if it does
	 */
	private function _attributeValueExists($argAttribute, $argValue) {
		$attributeModel = Mage::getModel('eav/entity_attribute');
		$attributeOptionsModel = Mage::getModel('eav/entity_attribute_source_table');

		$attributeCode = $attributeModel->getIdByCode('catalog_product', $argAttribute);
		$attribute = $attributeModel->load($attributeCode);

		$attributeTable = $attributeOptionsModel->setAttribute($attribute);
		$options = $attributeOptionsModel->getAllOptions(false);

		foreach ($options as $option) {
			if (trim($option['label']) == trim($argValue)) {
				return $option['value'];
			}
		}
		return false;
	}

}