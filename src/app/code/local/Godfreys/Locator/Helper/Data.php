<?php

class Godfreys_Locator_Helper_Data extends Mage_Core_Helper_Abstract
{
    const  USE_DEFAULT =0;
    public function getStores(){
        $options = array();
        $options = Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true);
        return $options;
    }
}
