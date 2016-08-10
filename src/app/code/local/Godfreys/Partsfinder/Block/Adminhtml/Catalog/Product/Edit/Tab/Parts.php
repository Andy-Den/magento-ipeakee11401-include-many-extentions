<?php
class Godfreys_Partsfinder_Block_Adminhtml_Catalog_Product_Edit_Tab_Parts extends Mage_Adminhtml_Block_Widget
{
	public function __construct()
	{
		parent::__construct();
		$this->setTemplate('partsfinder/parts.phtml');
	}
	
	protected function _getProduct()
	{
		return Mage::registry('current_product');
	}
	
	protected function _prepareLayout()
	{
		$this->setChild('delete_button',
				$this->getLayout()->createBlock('adminhtml/widget_button')
				->setData(array(
						'label' => Mage::helper('eav')->__('Delete'),
						'class' => 'delete delete-option'
				)));
	
		$this->setChild('add_button',
				$this->getLayout()->createBlock('adminhtml/widget_button')
				->setData(array(
						'label' => Mage::helper('eav')->__('Add Relation'),
						'class' => 'add',
						'id'    => 'add_new_relation_button'
				)));
		
		$this->setChild('save_button',
				$this->getLayout()->createBlock('adminhtml/widget_button')
				->setData(array(
						'label' => Mage::helper('eav')->__('Save Relation'),
						'class' => 'save',
						'id'	=> 'save_relation_button'
						))
				);
		return parent::_prepareLayout();
	}
	
	public function getDeleteButtonHtml()
	{
		return $this->getChildHtml('delete_button');
	}
	
	public function getAddNewButtonHtml()
	{
		return $this->getChildHtml('add_button');
	}
	
	public function getSaveButtonHtml()
	{
		return $this->getChildHtml('save_button');
	}
	
	public function getBrands()
	{
		return Mage::getModel('partsfinder/brand')->loadAll(false);
	}
	
	public function getModels($brandId)
	{
		
	}
	
	public function getProducts($brandId, $modelId) 
	{
		
	}
	
	public function getRelations()
	{
		$result = Mage::getModel('partsfinder/accessory')->getRelationsById($this->_getProduct()->getId(), false);
		for($i=0; $i < count($result); $i++) {
			$result[$i]['id'] = $i . '';
			$result[$i]['product_name'] = $result[$i]['name'];
			unset($result[$i]['name']);
			unset($result[$i]['accessory_id']);
		}
		return $result;
	}
}