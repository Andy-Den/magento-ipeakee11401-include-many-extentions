<?php
class AHT_Backupcms_Block_Adminhtml_Importpage extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_backupcms';
		$this->_blockGroup = 'backupcms';
		$this->_headerText = Mage::helper('backupcms')->__('Import CMS Pages');
		parent::__construct();
		$this->_removeButton('add');
	}
}