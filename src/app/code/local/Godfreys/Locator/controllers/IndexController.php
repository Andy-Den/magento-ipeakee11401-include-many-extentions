<?php
class Godfreys_Locator_IndexController extends Mage_Core_Controller_Front_Action
{
    public function importStoresAction()
    {
        echo "You can't run script here";
        return;
        $importer = Mage::getModel('godfreys_locator/import');
        $importer->run();
        $this->getResponse()->setBody("Import stores completely!");

    }

    public function updateOldStoreAction(){
        echo "You can't run script here";
        return;
        // Bulk update to assign existing Godfreys stores to the Godfreys (AU and NZ) website(s)
        $locationCollection = Mage::getModel('ak_locator/location')->getCollection();
        if ($locationCollection->getSize()) {
            foreach ($locationCollection as $row) {
                $locationDetail = Mage::getModel('ak_locator/location') ->load($row->getId());
                if (strtolower($locationDetail->getCountry()) == "australia") {
                    $locationDetail->setWebsiteBasedStores(2);
                }
                elseif (strtolower($locationDetail->getCountry()) == "new zealand") {
                    $locationDetail->setWebsiteBasedStores(3);
                }
                $locationDetail->save();
            }
        }
        echo "Update Old stores location completely";
    }
}