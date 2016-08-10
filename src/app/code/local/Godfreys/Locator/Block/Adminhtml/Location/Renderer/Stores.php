<?php
class Godfreys_Locator_Block_Adminhtml_Location_Renderer_Stores extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row) {
        $storeIds = $row->getWebsiteBasedStores();
        $strStore = "";
        if ($storeIds != "") {
           $storeIdArray = explode(",",$storeIds);

           if (count($storeIdArray)) {
                foreach ($storeIdArray as $storeId) {
                    if($storeId==0) {
                        $strStore = "All Store Views";
                        return $strStore;
                    }
                    $storeDetail = Mage::getModel('core/store')->load($storeId);
                    $storeName = $storeDetail->getName();
                    $strStore .= "<strong>".$storeDetail->getWebsite()->getName()."</strong><br/><strong>".$storeDetail->getGroup()->getName()."</strong><br>".$storeName."<br><br>";
                }
           }
        }
        return $strStore;
    }
}
