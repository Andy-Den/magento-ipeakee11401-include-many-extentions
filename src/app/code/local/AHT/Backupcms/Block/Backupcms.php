<?php
class AHT_Backupcms_Block_Backupcms extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getBackupcms()     
     { 
        if (!$this->hasData('backupcms')) {
            $this->setData('backupcms', Mage::registry('backupcms'));
        }
        return $this->getData('backupcms');
        
    }
}