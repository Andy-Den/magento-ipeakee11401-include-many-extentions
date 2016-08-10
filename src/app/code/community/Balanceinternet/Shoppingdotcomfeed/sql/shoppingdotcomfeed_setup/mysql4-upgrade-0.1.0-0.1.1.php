<?php

$installer = $this;
$installer->startSetup();
$installer->run("ALTER TABLE `{$this->getTable('sdc_feed')}` ADD COLUMN `error` TEXT AFTER successful_export");
$installer->endSetup();
