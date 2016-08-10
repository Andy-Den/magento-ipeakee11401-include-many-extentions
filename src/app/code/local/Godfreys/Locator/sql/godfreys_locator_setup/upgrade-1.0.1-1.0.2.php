<?php
/* @var $installer ak_locator_Model_Resource_Setup */
$installer = $this;
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->removeAttribute(Ak_Locator_Model_Location::ENTITY,'enable_voucher_block');

$installer->addAttribute(Ak_Locator_Model_Location::ENTITY, 'enable_voucher_block', array(
    'input'             => 'select',
    'type'              => 'int',
    'label'             => 'Enable Voucher Block',
    'backend_label'     => 'Enable Voucher Block',
    'source'            => 'eav/entity_attribute_source_boolean',
    'user_defined'      => false,
    'visible'           => 1,
    'required'          => 0,
    'position'          => 20,
    'default'           => 1,
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));

$eavConfig = Mage::getSingleton('eav/config');
$attribute = $eavConfig->getAttribute(Ak_Locator_Model_Location::ENTITY, 'enable_voucher_block');
$attribute->setData('used_in_forms', array('location_edit','location_create'));
$attribute->save();

$installer->endSetup();