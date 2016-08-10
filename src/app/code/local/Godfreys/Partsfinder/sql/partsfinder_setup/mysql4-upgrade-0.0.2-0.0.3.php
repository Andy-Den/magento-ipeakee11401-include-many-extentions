<?php
$installer  = $this;

$installer->addAttribute('catalog_category', 'brand_id', array(
		'backend'       => '',
		'frontend'      => '',
		'class' => '',
		'default'       => '',
		'label' => 'Brand',
		'input' => 'select',
		'type'  => 'int',
		'source'        => 'catalog/category_attribute_source_brand',
		'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
		'visible'       => 1,
		'required'      => 0,
		'searchable'    => 0,
		'filterable'    => 0,
		'unique'        => 0,
		'comparable'    => 0,
		'visible_on_front' => 1,
		'is_html_allowed_on_front' => 0,
		'user_defined'  => 1,
));

$installer->addAttributeToSet('catalog_category', 3, 'General Information', 'brand_id');

/*
INSERT INTO `catalog_category_entity_int`(`entity_type_id`, `attribute_id`, `store_id`, `entity_id`, `value`)
SELECT 3, 369, 0, e.`entity_id`, b.`brand_id` AS `value` FROM `catalog_category_entity` AS e
INNER JOIN `catalog_category_entity_varchar` AS vc ON e.`entity_id`=vc.`entity_id` AND vc.`attribute_id`=35 AND vc.`store_id`=0
INNER JOIN `partsfinder_brand` AS b ON vc.`value`=b.`brand_name`
ON DUPLICATE KEY UPDATE `value`=VALUES(`value`)
 * 
 */