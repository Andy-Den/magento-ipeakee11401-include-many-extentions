<?php
require_once __DIR__.DIRECTORY_SEPARATOR.'/../abstract.php'; // Use absolute path to have it working well with crontab.
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
function assist($e)
{
    print_r($e);
    echo PHP_EOL;
}
ini_set('display_errors', 1);

/**
 * Class Printforce_Package_TestVersion
 *
 * @author Derek Li (derek@balanceinternet.com.au)
 */
class Shell_Godfreys_CelebrosSalespersionCatalogUpdate extends Mage_Shell_Abstract
{
    /**
     * Run script
     *
     */
    public function run()
    {
        Mage::getModel('salesperson/observer')->catalogUpdate();
    }
}

try {
    $shell = new Shell_Godfreys_CelebrosSalespersionCatalogUpdate();
    $shell->run();
} catch (Exception $e) {
    echo $e->getMessage().PHP_EOL;
}