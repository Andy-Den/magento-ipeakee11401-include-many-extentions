<?php

class AHT_Backupcms_Adminhtml_Backupcms_BackupcmsController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('cms/backup')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction();
		$this->_title('Backup Pages');
		$this->_addContent($this->getLayout()->createBlock('backupcms/adminhtml_backupcms'));
		$this->renderLayout();
	}
	
	public function staticAction() {
		$this->_initAction();
		$this->_title('Backup Static Blocks');
		$this->_addContent($this->getLayout()->createBlock('backupcms/adminhtml_backupcms'));
		$this->renderLayout();
	}
	
	public function importpageAction() {
		$this->_initAction();
		$this->_title('Import CMS Page');
		$this->_addContent($this->getLayout()->createBlock('backupcms/adminhtml_importpage'));
		$this->renderLayout();
	}
	
	public function importstaticAction() {
		$this->_initAction();
		$this->_title('Import Static Blocks');
		$this->_addContent($this->getLayout()->createBlock('backupcms/adminhtml_importstatic'));
		$this->renderLayout();
	}
}