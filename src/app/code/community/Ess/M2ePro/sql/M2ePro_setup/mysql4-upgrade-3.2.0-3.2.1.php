<?php

//#############################################

/** @var $installer Ess_M2ePro_Model_Upgrade_MySqlSetup */
$installer = $this;
$installer->startSetup();

//#############################################

$installer->run(<<<SQL

ALTER TABLE m2epro_order_item
MODIFY `order_id` INT(11) UNSIGNED NOT NULL;

ALTER TABLE `m2epro_ebay_order_item`
MODIFY `transaction_id` VARCHAR(20) NOT NULL;

ALTER TABLE `m2epro_ebay_feedback`
MODIFY `ebay_transaction_id` VARCHAR(20) NOT NULL;

SQL
);

$generalOrderItemTable = $installer->getTable('m2epro_order_item');

if ($installer->getConnection()->tableColumnExists($generalOrderItemTable, 'update_date') === false) {
    $installer->getConnection()->addColumn($generalOrderItemTable, 'update_date', 'DATETIME DEFAULT NULL AFTER `component_mode`');
}

if ($installer->getConnection()->tableColumnExists($generalOrderItemTable, 'create_date') === false) {
    $installer->getConnection()->addColumn($generalOrderItemTable, 'create_date', 'DATETIME DEFAULT NULL AFTER `update_date`');
}

$generalOrderItemTableIndexList = $installer->getConnection()->getIndexList($generalOrderItemTable);
$productIdIndexName = strtoupper('product_id');

if (!isset($generalOrderItemTableIndexList[$productIdIndexName])) {
    $installer->getConnection()->addKey($generalOrderItemTable, 'product_id', 'product_id');
}

//#############################################

$installer->endSetup();

//#############################################