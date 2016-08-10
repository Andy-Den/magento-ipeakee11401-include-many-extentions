<?php
require_once '../abstract.php';
set_time_limit(0);
ini_set("memory_limit","2000M");

class Mage_Shell_Godfrey_Importhhoverstores extends Mage_Shell_Abstract
{

    function run()
        {
            if( $this->getArg('updateold')) {
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
                        echo "updateded: ".$locationDetail->getTitle()."\n";
                    }
                }
                echo "Update Old stores location completely";
            }
            else if ($this->getArg('importhover')) {
                $importer = Mage::getModel('godfreys_locator/import');
                $importer->run();
                echo "Import stores completely!";
            }
            else {
                echo $this->usageHelp();
            }
        }



    /**
     * Retrieve Usage Help Message
     *
     */
    public function usageHelp()
    {
        return <<<USAGE
Usage:  php export_hover_stores.php -- [options]
   Note:
    1. update first: you have to run script update oldoldstores first
    2. Need to put file csv into folder app\code\local\Godfreys\Locator\data before run import
    3. Option:
        --updateold run update store locations for godfreys script
        --importhover run import hover stores script.

USAGE;
    }
}


$shell = new Mage_Shell_Godfrey_Importhhoverstores();
$shell->run();

