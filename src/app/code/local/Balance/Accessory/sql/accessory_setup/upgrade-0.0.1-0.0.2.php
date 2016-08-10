<?php

// Adds in new attributes and the accessory attribute set
$installer = $this;
/* @var $installer Balance_Accessory_Model_Resource_Setup */

$installer->startSetup();
$entityTypeId = $installer->getEntityTypeId('catalog_product');
$setName = 'Accessories';
$setCode = 'accessories';
$groupName = 'Accessory Details';

//save atttribute set

$model = Mage::getModel('eav/entity_attribute_set')
        ->setEntityTypeId($entityTypeId);
$model->setAttributeSetName($setName);
$model->setAttributeCode($setCode);
try {
    $model->save();
    $model->initFromSkeleton($installer->getDefaultAttributeSetId('catalog_product'));
    $model->save();
}
catch (Exception $ex) {
    // Already exists
}

//get set id and save group
$setId = $installer->getAttributeSetId($entityTypeId, $setName);
$installer->addAttributeGroup($entityTypeId, $setId, $groupName, 20);
$groupId = $installer->getAttributeGroupId($entityTypeId, $setId, $groupName);

// add the brand attribute
try {
    $installer->addAttribute('catalog_product', 'accessory_brands', array(
        'attribute_set' => $setCode,
        'group' => 'Accessory Details',
        'label' => 'Applicable Brands',
        'type' => 'varchar',
        'input' => 'multiselect',
        'backend' => 'eav/entity_attribute_backend_array',
        'frontend' => '',
        'source' => '',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'visible' => true,
        'required' => false,
        'is_user_defined' => true,
        'searchable' => false,
        'filterable' => true,
        'comparable' => false,
        'option' => array(),
        'visible_on_front' => false,
        'visible_in_advanced_search' => false,
        'unique' => false
    ));
}
catch (Exception $ex) {
    // already exists
}


// add the brand attribute
try {

    $installer->addAttribute('catalog_product', 'accessory_models', array(
        'attribute_set' => $setCode,
        'group' => 'Accessory Details',
        'label' => 'Applicable Models',
        'type' => 'varchar',
        'input' => 'multiselect',
        'backend' => 'eav/entity_attribute_backend_array',
        'frontend' => '',
        'source' => '',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'visible' => true,
        'required' => false,
        'is_user_defined' => true,
        'searchable' => false,
        'filterable' => true,
        'comparable' => false,
        'option' => array(),
        'visible_on_front' => false,
        'visible_in_advanced_search' => false,
        'unique' => false
    ));
}
catch (Exception $ex) {
    // already exists
}
$installer->endSetup();