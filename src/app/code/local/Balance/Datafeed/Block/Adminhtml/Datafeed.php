<?php
class Balance_Datafeed_Block_Adminhtml_Datafeed extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_datafeed';
		$this->_blockGroup = 'datafeed';
		$this->_headerText = Mage::helper('datafeed')->__('Balance Data Feed');
		$this->_addButtonLabel = Mage::helper('datafeed')->__('Create new data feed');
		parent::__construct();
	}
}

