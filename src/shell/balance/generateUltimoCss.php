<?php
require_once '../abstract.php';
require_once '../../app/Mage.php';

/**
 * Class Balance_Shell_GenerateUltimoCss
 *
 * @package Balance_Shell
 * @author  Balance Internet Team (dev@balanceinternet.com.au)
 */
class Balance_Shell_GenerateUltimoCss extends Mage_Shell_Abstract
{
    /**
     * The main function.
     *
     * @return void
     */
    public function run()
    {
        echo "----Start: Balance_Shell_GenerateUltimoCss---\n";
        $this->process();
        echo "----End: Balance_Shell_GenerateUltimoCss---\n";
    }

    /**
     * Process the store codes.
     *
     * @return void
     */
    public function process()
    {
        $this->runUltimo(null, null);

        $websites= Mage::app()->getWebsites();
        foreach ($websites as $website) {
            $websiteCode = $website->getCode();
            $storeCode = null;
            $this->runUltimo($websiteCode, $storeCode);

            foreach ($website->getGroups() as $group) {
                $stores = $group->getStores();
                foreach ($stores as $store) {
                    $websiteCode = $website->getCode();
                    $storeCode = $store->getCode();
                    $this->runUltimo($websiteCode, $storeCode);
                }
            }
        }
    }

    /**
     * Generates the css using website data.
     *
     * @param String $websiteCode Website The website code.
     * @param String $storeCode   The store code.
     *
     * @return void
     */
    public function runUltimo($websiteCode, $storeCode)
    {
        echo "Start: Generating CSS when website is:". $websiteCode. " and Store is:".$storeCode."\n";

        Mage::getSingleton('ultimo/cssgen_generator')->generateCss('grid',   $websiteCode, $storeCode);
        Mage::getSingleton('ultimo/cssgen_generator')->generateCss('layout', $websiteCode, $storeCode);
        Mage::getSingleton('ultimo/cssgen_generator')->generateCss('design', $websiteCode, $storeCode);

        echo "Complete: Generating CSS when website is:". $websiteCode. " and Store is:".$storeCode."\n";

    }
}

$shell = new Balance_Shell_GenerateUltimoCss();
$shell->run();