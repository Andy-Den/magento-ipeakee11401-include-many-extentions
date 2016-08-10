<?php
$installer = $this;

$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS {$this->getTable('partsfinder_brand')};
CREATE TABLE IF NOT EXISTS {$this->getTable('partsfinder_brand')} (
`brand_id` int UNSIGNED NOT NULL AUTO_INCREMENT,
`brand_name` varchar(64) NOT NULL,
`option_id` int UNSIGNED NOT NULL,
`sort_order` int NOT NULL DEFAULT 0,
PRIMARY KEY (`brand_id`),
UNIQUE KEY `UNIQUE_BRAND_NAME` (`brand_name`),
KEY `option_id` (`option_id`)
) ENGINE=InnoDB;		

DROP TABLE IF EXISTS {$this->getTable('partsfinder_model')};
CREATE TABLE IF NOT EXISTS {$this->getTable('partsfinder_model')} (
`model_id` int UNSIGNED NOT NULL AUTO_INCREMENT,
`model_name` varchar(64) NOT NULL,
`brand_id` int UNSIGNED NOT NULL,
PRIMARY KEY (`model_id`),
KEY `model_name` (`model_name`),
KEY `parent_id` (`brand_id`),
UNIQUE KEY `UNIQUE_MODEL_NAME_MANUFACTURER` (`model_name`, `brand_id`),
FOREIGN KEY (`brand_id`) REFERENCES `partsfinder_brand`(`brand_id`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS {$this->getTable('partsfinder_accessory_relation')};
CREATE TABLE IF NOT EXISTS {$this->getTable('partsfinder_accessory_relation')} (
`accessory_id` int UNSIGNED NOT NULL,
`brand_id` int UNSIGNED NOT NULL,
`model_id` int UNSIGNED NOT NULL,
`product_id` int UNSIGNED NOT NULL,
`name` varchar(255),
`sku` varchar(255),
PRIMARY KEY (`accessory_id`, `brand_id`, `model_id`),
KEY `filter_id` (`brand_id`, `model_id`),
FOREIGN KEY (`brand_id`) REFERENCES `partsfinder_brand`(`brand_id`),
FOREIGN KEY (`model_id`) REFERENCES `partsfinder_model`(`model_id`)
) ENGINE=InnoDB;

");

/*
$installer->run("
INSERT INTO `partsfinder_brand` SELECT NULL, v.`value` AS `name`, v.`option_id`, o.`sort_order` FROM `eav_attribute_option` AS o
INNER JOIN `eav_attribute_option_value` AS v
ON o.`option_id`=v.`option_id`
WHERE o.`attribute_id`=168 and v.`store_id`=0
ORDER BY o.`sort_order`;

INSERT INTO `partsfinder_model` 
SELECT NULL, v.`value` as `model_name`, b.`brand_id` FROM `catalog_product_entity_int` AS i 
INNER JOIN `catalog_product_entity_varchar` AS v 
ON i.`entity_id`=v.`entity_id` AND i.`attribute_id`=168 AND i.`store_id`=0 AND i.`value` IS NOT NULL
AND v.`attribute_id`=229 AND v.`store_id`=0 AND v.`value` <>''
INNER JOIN `partsfinder_brand` AS b ON i.`value`=b.`option_id`
;

insert into partsfinder_accessory_relation 
select l.product_id as accessory_id, b.brand_id, m.model_id, e.entity_id AS product_id, n.value AS name, e.sku from 
catalog_product_entity as e
inner join catalog_product_entity_int as i on e.entity_id=i.entity_id and i.attribute_id=168
inner join catalog_product_entity_varchar as v on e.entity_id=v.entity_id and v.attribute_id=229
inner join catalog_product_link as l on e.entity_id=l.linked_product_id AND l.link_type_id=6
inner join partsfinder_brand as b ON i.value=b.option_id
inner join partsfinder_model as m on v.value=m.model_name AND m.`brand_id`=b.brand_id
inner join catalog_product_entity_varchar as n on e.entity_id=n.entity_id AND n.attribute_id=65
;
");
*/