<?php

class Balanceinternet_Shoppingdotcomfeed_Block_Adminhtml_Shoppingdotcomfeed_Renderer_Frequency extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        return Mage::getModel('shoppingdotcomfeed/feedportal')->getResource()->getFrequenciesForManageFeedGrid($row->getData($this->getColumn()->getIndex()));
    }
    /*
    public function render(Varien_Object $row) {
        $idFrequency = $row->getData($this->getColumn()->getIndex());
        $conn = Mage::getSingleton('core/resource')->getConnection('core_read');
        $sdcUpdateFrequency = $conn->fetchAll("SELECT label FROM sdc_updatefrequency WHERE id = ?", array($idFrequency));
        return $sdcUpdateFrequency[0]['label'];
    } 
     */   
    

}

