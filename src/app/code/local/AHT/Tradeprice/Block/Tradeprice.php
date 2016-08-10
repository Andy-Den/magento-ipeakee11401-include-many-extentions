<?php
class AHT_Tradeprice_Block_Tradeprice extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getTradeprice()     
     { 
        if (!$this->hasData('tradeprice')) {
            $this->setData('tradeprice', Mage::registry('tradeprice'));
        }
        return $this->getData('tradeprice');
        
    }
}