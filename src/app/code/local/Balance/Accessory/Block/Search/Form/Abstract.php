<?php

/**
 * The mini search form that appears around the site
 */
abstract class Balance_Accessory_Block_Search_Form_Abstract extends Mage_Core_Block_Template {

	public function getMiniSearchPostUrl()
	{
		return $this->getUrl('catalog/category/view', array(
                    'id' => $this->getModel()->getCategoryId(),
		));
	}

	/**
	 * Retrieve advanced search model object
	 *
	 * @return Mage_CatalogSearch_Model_Advanced
	 */
	public function getModel()
	{
		return Mage::getSingleton('accessory/catalog_product_accessory');
	}

	/**
	 * Get all of the availble select option ids for the brand
	 * based on the current category
	 */
	protected function _getAvailableBrandsIdsForCategory()
	{
		/* @var $helper Balance_Accessory_Helper_Data */
		$helper = Mage::helper('accessory');
			$collection = $helper->getCurrentStoreAccessories()
			->addAttributeToSelect('*')
			->load();

		// Go through each of the accessories
		// Get the brand and add it to the array
		$brandIds = array();
		// $brandIds is the array containing the option ids in the dropdown
		$model = Mage::getModel('accessory/catalog_product_accessory');
		$accessoryBrandsCode = $model->getAccessoryBrandsAttribute()->getAttributeCode();
		foreach($collection as $accessory) {
			$accessoryBrandIds = explode(',',$accessory->getData($accessoryBrandsCode));
			foreach($accessoryBrandIds as $accessoryBrandId) {
				if(!in_array($accessoryBrandId, $brandIds)) {
					$brandIds[] = $accessoryBrandId;
				}
			}
		}
		return $brandIds;
	}

	/**
	 * Get all of the availble select option ids for the brand
	 * based on the current category
	 */
	protected function _getAvailableModelIdsForCategory()
	{
		/* @var $helper Balance_Accessory_Helper_Data */
		$helper = Mage::helper('accessory');
		$collection = $helper->getCurrentStoreAccessories()
		->addAttributeToSelect('*')
		->load();

		// Go through each of the accessories
		// Get the brand and add it to the array
		$modelIds = array();
		// $brandIds is the array containing the option ids in the dropdown
		$model = Mage::getModel('accessory/catalog_product_accessory');
		$accessoryModelsCode = $model->getAccessoryModelsAttribute()->getAttributeCode();
		foreach($collection as $accessory) {
			$accessoryModelIds = explode(',',$accessory->getData($accessoryModelsCode));
			foreach($accessoryModelIds as $accessoryModelId) {
				if(!in_array($accessoryModelId, $modelIds)) {
					$modelIds[] = $accessoryModelId;
				}
			}
		}
		return $modelIds;
	}


	/**
	 * Retrieve attribute input type
	 *
	 * @param   $attribute
	 * @return  string
	 */
	public function getAttributeInputType($attribute)
	{
		$dataType = $attribute->getBackend()->getType();
		$inputType = $attribute->getFrontend()->getInputType();
		if ($inputType == 'select' || $inputType == 'multiselect') {
			return 'select';
		}
		else {
			Mage::throwException("Data type of " . $dataType . " is not yet supported for accessory minisearch");
		}
	}

	/**
	 * Filter options based on the current store
	 * @param unknown_type $attribute
	 * @param unknown_type $options
	 */
	protected function _filterOptions($attribute, $options)
	{
		if($attribute->getAttributeCode() == Mage::getModel('accessory/catalog_product_accessory')->getAccessoryBrandsAttribute()->getAttributeCode()) {
			$cleanedOptions = array();
			$allowedBrandIds = $this->_getAvailableBrandsIdsForCategory();
			foreach($options as $option) {
				if(in_array($option['value'], $allowedBrandIds)) {
					$cleanedOptions[] = $option;
				}
			}
			$options = $cleanedOptions;
		}
		
		if($attribute->getAttributeCode() == Mage::getModel('accessory/catalog_product_accessory')->getAccessoryModelsAttribute()->getAttributeCode()) {
			$cleanedOptions = array();
			$allowedModelIds = $this->_getAvailableModelIdsForCategory();
			foreach($options as $option) {
				if(in_array($option['value'], $allowedModelIds)) {
					$cleanedOptions[] = $option;
				}
			}
			$options = $cleanedOptions;
		}
		return $options;
	}

	/**
	 * Get a selectable element
	 * @param type $attribute
	 * @return type
	 */
	public function getAttributeSelectElement($attribute)
	{
		$extra = '';
		$options = $attribute->getSource()->getAllOptions(false);
		$options = $this->_filterOptions($attribute, $options);
		$name = $attribute->getAttributeCode();

		// 2 - avoid yes/no selects to be multiselects
		if (is_array($options) && count($options) > 2) {
			//$extra = 'multiple="multiple" size="4"';
			//$name.= '[]';
		}
		else {

		}
		$options = $this->_aasort($options,'label');
		//array_unshift($options, array('value' => '', 'label' => Mage::helper('catalogsearch')->__('All ' . $attribute->getFrontend()->getLabel() . 's')));
		array_unshift($options, array('value' => '', 'label' => Mage::helper('catalogsearch')->__('Select ' . $attribute->getFrontend()->getLabel())));


		return $this->_getSelectBlock()
		->setName($name)
		->setId($attribute->getAttributeCode())
		->setTitle($attribute->getFrontend()->getLabel())
		->setExtraParams($extra)
		->setValue($this->getAttributeValue($attribute))
		->setOptions($options)
		->setClass('select')
		->getHtml();
	}

	/**
	 * Sort a multidimensional array by key
	 * @param type $array
	 * @param type $key
	 * @return type
	 */
	protected function _aasort(&$array, $key)
	{
		$sorter = array();
		$ret = array();
		reset($array);
		foreach ($array as $ii => $va) {
			$sorter[$ii] = $va[$key];
		}
		asort($sorter);
		foreach ($sorter as $ii => $va) {
			$ret[$ii] = $array[$ii];
		}
		$array = $ret;
		return $array;
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

	/**
	 * Get the attributes that can be used to search
	 */
	abstract protected function getSearchAttributes();
}