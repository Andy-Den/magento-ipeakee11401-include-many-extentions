<?php

/** @var $installer Balance_Accessory_Model_Resource_Setup */
$installer = $this;

/**
 * Prepare database before module installation
 */
$installer->startSetup();
$installer->run("INSERT INTO " . $installer->getTable('accessory/product_link_type') . " VALUES ('6', 'accessory')");
$installer->endSetup();