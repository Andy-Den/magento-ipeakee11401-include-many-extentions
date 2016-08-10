<?php
class Godfreys_Partsfinder_Model_Category_Attribute_Source_Brand extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
	public function getAllOptions()
	{
		if (!$this->_options) {
			$this->_options = array();
			$brands = Mage::getModel('partsfinder/brand')->loadAll(false);
			foreach($brands as $brand) {
				$this->_options[] = array('value'=> $brand['id'], 'label' => $brand['name']);
			}
			//$this->_options = Mage::getSingleton('page/source_layout')->toOptionArray();
			
			array_unshift($this->_options, array('value'=>'', 'label'=>Mage::helper('catalog')->__('Not a brand category')));
		}
		return $this->_options;
	}
}
