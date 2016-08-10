<?php

class Balance_Datafeed_Block_Adminhtml_Datafeed_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct()
	{
		parent::__construct();
		 
		$this->_objectId = 'feed_id';
		$this->_blockGroup = 'datafeed';
		$this->_controller = 'adminhtml_datafeed';
			
		
		if(Mage::registry('datafeed_data')->getFeedId()){
		    	$this->_addButton('generate', array(
		            'label'   => Mage::helper('adminhtml')->__('Save & Generate'),
		            'onclick' => "$('generate').value=1; editForm.submit();",
		            'class'   => 'add',
		        ));
		        $this->_addButton('continue', array(
		            'label'   => Mage::helper('adminhtml')->__('Save & Continue'),
		            'onclick' => "$('continue').value=1; editForm.submit();",
		            'class'   => 'add',
		        ));
		        $this->_addButton('copy', array(
		            'label'   => Mage::helper('adminhtml')->__('Copy'),
		            'onclick' => "$('feed_id').remove(); editForm.submit();",
		            'class'   => 'add',
		        ));
		    }    
		
	}

	public function getHeaderText()
	{
		if( Mage::registry('datafeed_data') && Mage::registry('datafeed_data')->getFeedId() ) {
			return Mage::helper('datafeed')->__("Edit data feed  '%s'", $this->htmlEscape(Mage::registry('datafeed_data')->getFeed_name()));
		} else {
			return Mage::helper('datafeed')->__('Add data feed');
		}
	}
}