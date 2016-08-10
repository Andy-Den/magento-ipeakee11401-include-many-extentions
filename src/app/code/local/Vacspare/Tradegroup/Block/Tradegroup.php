<?php
class Vacspare_Tradegroup_Block_Tradegroup extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getTradegroup()     
     { 
        if (!$this->hasData('tradegroup')) {
            $this->setData('tradegroup', Mage::registry('tradegroup'));
        }
        return $this->getData('tradegroup');
        
    }
}