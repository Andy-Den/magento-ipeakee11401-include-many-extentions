<?php
/**
 * The mini search form that appears around the site
 */
class Balance_Accessory_Block_Search_Form_Model_Dropdown extends Balance_Accessory_Block_Search_Form_Abstract {

	/**
	 * Set the brand
	 * @var type
	 */
	private $_brandId = null;

	/**
	 * Set the brand to narrow by
	 * @param string $brand
	 */
	public function setBrand($brand){
		$this->_brandId = $brand;
	}
	
	/**
	 * Filter the models list by a brand selection
	 * @param type $brand
	 */
	public function filterModelsByBrand()
	{

		// 1. Get all of the accessorys that match the brand selected
		$model = Mage::getModel('accessory/catalog_product_accessory');
		$collection = Mage::helper('accessory')
		->getAllAccessories()
		->addAttributeToFilter(
		Mage::getStoreConfig(Balance_Accessory_Model_Catalog_Layer_Filter_Accessory_Brand::XML_PATH_ATTRIBUTE_CODE),
		array('finset' => $this->_brandId))
		->addAttributeToSelect('*')
		->load();

		// 2. load in each of the related products for each accessory
		// check if the brand is the one that's selected
		// if it's not, discard options from the
		/** @var $accessory Mage_Catalog_Model_Product */
		$productBrand = Mage::getStoreConfig(Balance_Accessory_Model_Catalog_Product_Accessory::XML_PATH_PRODUCT_BRAND_ATTRIBUTE_CODE);
		$productModel = Mage::getStoreConfig(Balance_Accessory_Model_Catalog_Product_Accessory::XML_PATH_PRODUCT_MODEL_ATTRIBUTE_CODE);
		$brandValue = $this->_getAttributeValue(
		Mage::getStoreConfig(Balance_Accessory_Model_Catalog_Layer_Filter_Accessory_Brand::XML_PATH_ATTRIBUTE_CODE), $this->_brandId);
		$productBrandSelectId = $this->_attributeValueExists($productBrand, $brandValue);
		$productModels = array();

		// 3. Get the related products on the accessory. check if their brand matches the selected
		//    brand and if it does add their model to the list of models
		foreach($collection as $accessory) {
			$relatedCollection = $accessory->getRelatedProductCollection()
			->addAttributeToSelect('entity_id')
			->addAttributeToSelect($productModel)
			->addAttributeToSelect($productBrand);

			foreach($relatedCollection as $related) {
				if($related->getData($productBrand) == $productBrandSelectId && !in_array($related->getData($productModel), $productModels)) {
					$productModels[] = $related->getData($productModel);
				}
			}
		}
		$data = array();
		foreach($productModels as $label) {
			if($optionId = $this->_attributeValueExists(Mage::getStoreConfig(Balance_Accessory_Model_Catalog_Layer_Filter_Accessory_Model::XML_PATH_ATTRIBUTE_CODE), $label)) {
				$data[] = array('label' => $label,
                            'value' => $optionId
				);
			}
		}
		return $this->_getSelectElement($model->getAccessoryModelsAttribute(), $data);
	}

	/**
	 * Get the value for an attribute using option id
	 * @param string $argAttribute - the attribute name
	 * @param int $argOptionId - the option id
	 * @return string value
	 */
	private function _getAttributeValue($argAttribute, $argOptionId)
	{
		$attributeModel = Mage::getModel('eav/entity_attribute');
		$attributeTable = Mage::getModel('eav/entity_attribute_source_table');

		$attributeCode = $attributeModel->getIdByCode('catalog_product', $argAttribute);
		$attribute = $attributeModel->load($attributeCode);

		$attributeTable->setAttribute($attribute);

		$option = $attributeTable->getOptionText($argOptionId);


		return $option;
	}

	/**
	 * Check if a value for an attribute exists
	 * @param string $argAttribute - the attribute name
	 * @param string $argValue - the attribute value
	 * @return false is attribute's value doesn't exist, or the attributes value if it does
	 */
	private function _attributeValueExists($argAttribute, $argValue)
	{
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


	/**
	 * Get a selectable element
	 * @param type $attribute
	 * @return type
	 */
	protected function _getSelectElement($attribute, $options)
	{

		$options = $this->_aasort($options,'label');
		array_unshift($options, array('value'=>'', 'label'=>Mage::helper('catalogsearch')->__('All ' . $attribute->getFrontend()->getLabel() . 's')));
		array_unshift($options, array('value'=>'', 'label'=>Mage::helper('catalogsearch')->__('Select ' . $attribute->getFrontend()->getLabel())));
		$name = $attribute->getAttributeCode();
		return $this->_getSelectBlock()
		->setName($name)
		->setId($attribute->getAttributeCode())
		->setTitle($attribute->getFrontend()->getLabel())
//		->setExtraParams($extra)
		->setValue($this->getAttributeValue($attribute))
		->setOptions($options)
		->setClass('select')
		->getHtml();
	}

	/**
	 * Get select block
	 * @return type
	 */
	protected function _getSelectBlock()
	{
		$block = $this->getData('_select_block');
		if (is_null($block)) {
			$block = $this->getLayout()->createBlock('core/html_select');
			$this->setData('_select_block', $block);
		}
		return $block;
	}

	protected function getSearchAttributes() {}

}