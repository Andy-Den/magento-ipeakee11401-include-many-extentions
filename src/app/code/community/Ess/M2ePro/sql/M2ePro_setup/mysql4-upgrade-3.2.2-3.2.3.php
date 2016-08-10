<?php

//#############################################

/** @var $installer Ess_M2ePro_Model_Upgrade_MySqlSetup */
$installer = $this;
$installer->startSetup();

//#############################################

$installer->run(<<<SQL

ALTER TABLE `m2epro_order`
MODIFY `account_id` INT(11) UNSIGNED NOT NULL;

SQL
);

$ebayAccountTable = $installer->getTable('m2epro_ebay_account');
$ebayAccountIndexList = $installer->getConnection()->getIndexList($ebayAccountTable);

if (!isset($ebayAccountIndexList['PRIMARY'])) {
    $installer->getConnection()->addKey($ebayAccountTable, 'PRIMARY', 'account_id', 'primary');
}

$ebayAccountIndexListToDrop = array(
    'server_hash',
    'orders_last_synchronization',
    'ebay_store_subscription_level',
    'ebay_store_title'
);

foreach ($ebayAccountIndexListToDrop as $indexName) {
    if (isset($ebayAccountIndexList[strtoupper($indexName)])) {
        $installer->getConnection()->dropKey($ebayAccountTable, $indexName);
    }
}

//#############################################

$installer->endSetup();

//#############################################