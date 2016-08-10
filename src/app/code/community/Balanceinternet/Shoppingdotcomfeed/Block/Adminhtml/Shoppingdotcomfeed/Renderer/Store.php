<?php

class Balanceinternet_Shoppingdotcomfeed_Block_Adminhtml_Shoppingdotcomfeed_Renderer_Store extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $storeId = $row->getData($this->getColumn()->getIndex());
        $storeName = Mage::app()->getStore($storeId)->getName() . ' - ' . Mage::app()->getStore($storeId)->getCode();
        return $storeName;
    }

}

