<?php
class AHT_Backupcms_Block_Adminhtml_Importstatic extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_backupcms';
		$this->_blockGroup = 'backupcms';
		if(Mage::app()->getRequest()->getActionName()!='static'){
			$this->_headerText = Mage::helper('backupcms')->__('Backup Pages');
		}
		else{
			$this->_headerText = Mage::helper('backupcms')->__('Backup Static Blocks');
		}
		parent::__construct();
		$this->_removeButton('add');
	}
}