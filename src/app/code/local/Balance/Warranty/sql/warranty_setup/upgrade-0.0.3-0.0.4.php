<?php
Mage::log("Upgrading Balance Warranty...");
$installer = $this;
/* @var $installer Balance_Warranty_Model_Resource_Setup */
$installer->startSetup();
$installer->addAttribute('customer', 'warranty_postcode', array(
    'type'     => 'varchar',
    'label'    => 'Warranty Postcode',
    'input'    => 'text',
    'visible'  => true,
    'system'   => false,
    'required' => false,
    'adminhtml_only' => 1,
    'used_in_forms' => array('customer_account_edit', 'adminhtml_customer')
));
$installer->addAttribute('customer', 'warranty_telephone', array(
    'type'     => 'varchar',
    'label'    => 'Warranty Phone Number',
    'input'    => 'text',
    'visible'  => true,
    'system'   => false,
    'required' => false,
    'adminhtml_only' => 1,
    'used_in_forms' => array('customer_account_edit', 'adminhtml_customer')
));
$installer->addAttribute('customer', 'offers_subscribed', array(
    'type'     => 'int',
    'label'    => 'Subscribed to offers?',
    'input'    => 'boolean',
    'visible'  => true,
    'system'   => false,
    'required' => false,
    'adminhtml_only' => 1,
    'used_in_forms' => array('customer_account_edit', 'adminhtml_customer')
));
$installer->addAttribute('customer', 'comps_subscribed', array(
    'type'     => 'int',
    'label'    => 'Subscribed to competitions?',
    'input'    => 'boolean',
    'visible'  => true,
    'system'   => false,
    'required' => false,
    'adminhtml_only' => 1,
    'used_in_forms' => array('customer_account_edit', 'adminhtml_customer')
));
$installer->addAttribute('customer', 'owns_pets', array(
    'type'     => 'int',
    'label'    => 'Owns Pets?',
    'input'    => 'boolean',
    'visible'  => true,
    'system'   => false,
    'required' => false,
    'adminhtml_only' => 1,
    'used_in_forms' => array('customer_account_edit', 'adminhtml_customer')
));
$installer->addAttribute('customer', 'children_under_18', array(
    'type'     => 'int',
    'label'    => 'Children under 18?',
    'input'    => 'boolean',
    'visible'  => true,
    'system'   => false,
    'required' => false,
    'adminhtml_only' => 1,
    'used_in_forms' => array('customer_account_edit', 'adminhtml_customer')
));
$installer->endSetup();