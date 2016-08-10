<?php
$installer = $this;

$installer->startSetup();

$installer->run("
	ALTER TABLE `{$this->getTable('celebrosfieldsmapping')}` 
	ADD COLUMN `store_id` INT(10) UNSIGNED NOT NULL DEFAULT 0 AFTER `id`;
	
	ALTER TABLE `{$this->getTable('celebrosfieldsmapping')}`
	DROP INDEX `XML_FIELD`
	;
	
	ALTER TABLE `{$this->getTable('celebrosfieldsmapping')}`
	ADD UNIQUE INDEX `XML_FIELD` (`store_id`, `xml_field`)
	;
");

$installer->endSetup();

/*
INSERT INTO `celebrosfieldsmapping` (`store_id`, `xml_field`, `code_field`) SELECT 2, `xml_field`, `code_field` FROM `celebrosfieldsmapping` WHERE store_id=0;
INSERT INTO `celebrosfieldsmapping` (`store_id`, `xml_field`, `code_field`) SELECT 3, `xml_field`, `code_field` FROM `celebrosfieldsmapping` WHERE store_id=0;
UPDATE  `celebrosfieldsmapping` SET `xml_field`='godfreys_au_link' WHERE store_id=2 AND `code_field`='link';
UPDATE  `celebrosfieldsmapping` SET `xml_field`='godfreys_nz_link' WHERE store_id=3 AND `code_field`='link';
UPDATE  `celebrosfieldsmapping` SET `xml_field`='godfreys_nz_image_link' WHERE store_id=3 AND `code_field`='image_link';
#UPDATE  `celebrosfieldsmapping` SET `xml_field`='godfreys_nz_price' WHERE store_id=3 AND `code_field`='price';
 */