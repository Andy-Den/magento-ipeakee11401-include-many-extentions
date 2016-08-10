<?php
class Godfreys_Partsfinder_Block_Adminhtml_Catalog_Product_Edit_Tabs extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs
{
	protected function _prepareLayout()
	{
		parent::_prepareLayout();
		$product = $this->getProduct();
	
		if (!($setId = $product->getAttributeSetId())) {
			$setId = $this->getRequest()->getParam('set', null);
		}
	
		if ($setId) {
	
			$this->addTab('parts', array(
				'label'     => Mage::helper('catalog')->__('Supported Machines'),
				//'url'       => $this->getUrl('*/*/partsfinder', array('_current' => true)),
				'url'       => $this->getUrl('*/adminpartsfinder_partsfinder/index', array('_current' => true)),
				'class'     => 'ajax',
			));
		}

		$this->addTabAfter('custom', array(
				'label'     => Mage::helper('catalog')->__('Warranty Products'),
				'url'       => $this->getUrl('*/adminpartsfinder_partsfinder/custom', array('_current' => true)),
				'class'     => 'ajax',
				),
			'related'
		);
		
		return parent::_prepareLayout();
	}
	
}