<?php
/* @var $installer ak_locator_Model_Resource_Setup */
$installer = $this;
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->removeAttribute(Ak_Locator_Model_Location::ENTITY,'store_fax');
$setup->removeAttribute(Ak_Locator_Model_Location::ENTITY,'store_email');
$setup->removeAttribute(Ak_Locator_Model_Location::ENTITY,'sotre_email');
$setup->removeAttribute(Ak_Locator_Model_Location::ENTITY,'website_based_stores');

$installer->addAttribute(Ak_Locator_Model_Location::ENTITY, 'website_based_stores', array(
    'type' => 'text',
    'input' => 'multiselect',
    'source' => 'godfreys_locator/entity_attribute_source_stores_list',
    'backend' => 'godfreys_locator/entity_attribute_backend_stores',
    'input_renderer' => 'godfreys_locator/adminhtml_data_form_stores',
    'label'         => 'Website based stores',
    'user_defined'  => false,
    'visible'       => 1,
    'required'      => 0,
    'wysiwyg_enabled' => true,
    'position'    => 350,
));

$installer->addAttribute(Ak_Locator_Model_Location::ENTITY, 'store_email', array(
    'input'         => 'text',
    'type'          => 'varchar',
    'label'         => 'Email',
    'backend'       => '',
    'user_defined'  => false,
    'visible'       => 1,
    'required'      => 0,
    'position'    => 351,
));


$installer->addAttribute(Ak_Locator_Model_Location::ENTITY, 'store_fax', array(
    'input'         => 'text',
    'type'          => 'varchar',
    'label'         => 'Fax',
    'backend'       => '',
    'user_defined'  => false,
    'visible'       => 1,
    'required'      => 0,
    'position'    => 352,
));

$formAttributes = array('store_fax','store_email','website_based_stores');

$eavConfig = Mage::getSingleton('eav/config');
foreach ($formAttributes as $code) {
    $attribute = $eavConfig->getAttribute(Ak_Locator_Model_Location::ENTITY, $code);
    $attribute->setData('used_in_forms', array('location_edit','location_create'));
    $attribute->save();
}
$installer->endSetup();