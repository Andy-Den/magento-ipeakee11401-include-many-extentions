<?php
/**
 * Location extension for Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright 2013 Andrew Kett. (http://www.andrewkett.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://andrewkett.github.io/Ak_Locator/
 */

/* @var $installer ak_locator_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->removeAttribute(Ak_Locator_Model_Location::ENTITY,'store_title');
$setup->removeAttribute(Ak_Locator_Model_Location::ENTITY,'store_address');
$setup->removeAttribute(Ak_Locator_Model_Location::ENTITY,'store_phone');
$setup->removeAttribute(Ak_Locator_Model_Location::ENTITY,'store_description');
$setup->removeAttribute(Ak_Locator_Model_Location::ENTITY,'store_serviced_suburbs');
$setup->removeAttribute(Ak_Locator_Model_Location::ENTITY,'store_hours_monday');
$setup->removeAttribute(Ak_Locator_Model_Location::ENTITY,'store_hours_tuesday');
$setup->removeAttribute(Ak_Locator_Model_Location::ENTITY,'store_hours_wednesday');
$setup->removeAttribute(Ak_Locator_Model_Location::ENTITY,'store_hours_thursday');
$setup->removeAttribute(Ak_Locator_Model_Location::ENTITY,'store_hours_friday');
$setup->removeAttribute(Ak_Locator_Model_Location::ENTITY,'store_hours_saturday');
$setup->removeAttribute(Ak_Locator_Model_Location::ENTITY,'store_hours_sunday');

$installer->addAttribute(Ak_Locator_Model_Location::ENTITY, 'store_title', array(
    'input'         => 'text',
    'type'          => 'varchar',
    'label'         => 'Name',
    'backend'       => '',
    'user_defined'  => false,
    'visible'       => 1,
    'required'      => 0,
    'position'    => 330,
));

$installer->addAttribute(Ak_Locator_Model_Location::ENTITY, 'store_address', array(
    'input'         => 'text',
    'type'          => 'text',
    'label'         => 'Address (standard address information) ',
    'backend'       => '',
    'user_defined'  => false,
    'visible'       => 1,
    'required'      => 0,
    'position'    => 331,
));
$installer->addAttribute(Ak_Locator_Model_Location::ENTITY, 'store_phone', array(
    'input'         => 'text',
    'type'          => 'varchar',
    'label'         => 'Phone Number',
    'backend'       => '',
    'user_defined'  => false,
    'visible'       => 1,
    'required'      => 0,
    'position'    => 332,
));

$installer->addAttribute(Ak_Locator_Model_Location::ENTITY, 'store_description', array(
    'input'         => 'textarea',
    'type'          => 'text',
    'label'         => 'Description',
    'backend'       => '',
    'user_defined'  => false,
    'visible'       => 1,
    'required'      => 0,
    'wysiwyg_enabled' => true,
    'position'    => 333,
));

$installer->addAttribute(Ak_Locator_Model_Location::ENTITY, 'store_serviced_suburbs', array(
    'input'         => 'textarea',
    'type'          => 'text',
    'label'         => 'Suburbs Serviced',
    'backend'       => '',
    'user_defined'  => false,
    'visible'       => 1,
    'required'      => 0,
    'wysiwyg_enabled' => true,
    'position'    => 334,
));
$installer->addAttribute(Ak_Locator_Model_Location::ENTITY, 'store_hours_monday', array(
    'input'         => 'text',
    'type'          => 'varchar',
    'label'         => 'Trading Hours - Monday',
    'backend'       => '',
    'user_defined'  => false,
    'visible'       => 1,
    'required'      => 0,
    'position'    => 335,
));

$installer->addAttribute(Ak_Locator_Model_Location::ENTITY, 'store_hours_tuesday', array(
    'input'         => 'text',
    'type'          => 'varchar',
    'label'         => 'Trading Hours - Tuesday',
    'backend'       => '',
    'user_defined'  => false,
    'visible'       => 1,
    'required'      => 0,
    'position'    => 336,
));

$installer->addAttribute(Ak_Locator_Model_Location::ENTITY, 'store_hours_wednesday', array(
    'input'         => 'text',
    'type'          => 'varchar',
    'label'         => 'Trading Hours - Wednesday',
    'backend'       => '',
    'user_defined'  => false,
    'visible'       => 1,
    'required'      => 0,
    'position'    => 337,
));


$installer->addAttribute(Ak_Locator_Model_Location::ENTITY, 'store_hours_thursday', array(
    'input'         => 'text',
    'type'          => 'varchar',
    'label'         => 'Trading Hours - Thursday',
    'backend'       => '',
    'user_defined'  => false,
    'visible'       => 1,
    'required'      => 0,
    'position'    => 338,
));

$installer->addAttribute(Ak_Locator_Model_Location::ENTITY, 'store_hours_friday', array(
    'input'         => 'text',
    'type'          => 'varchar',
    'label'         => 'Trading Hours - Friday',
    'backend'       => '',
    'user_defined'  => false,
    'visible'       => 1,
    'required'      => 0,
    'position'    => 339,
));

$installer->addAttribute(Ak_Locator_Model_Location::ENTITY, 'store_hours_saturday', array(
    'input'         => 'text',
    'type'          => 'varchar',
    'label'         => 'Trading Hours - Saturday',
    'backend'       => '',
    'user_defined'  => false,
    'visible'       => 1,
    'required'      => 0,
    'position'    => 340,
));
$installer->addAttribute(Ak_Locator_Model_Location::ENTITY, 'store_hours_sunday', array(
    'input'         => 'text',
    'type'          => 'varchar',
    'label'         => 'Trading Hours - Sunday',
    'backend'       => '',
    'user_defined'  => false,
    'visible'       => 1,
    'required'      => 0,
    'position'    => 341,
));




$formAttributes = array(
    'store_title',
    'store_phone',
    'store_address','store_description','store_serviced_suburbs','store_hours_monday','store_hours_tuesday',
    'store_hours_wednesday','store_hours_thursday','store_hours_friday','store_hours_saturday','store_hours_sunday'
);


$eavConfig = Mage::getSingleton('eav/config');

foreach ($formAttributes as $code) {
    $attribute = $eavConfig->getAttribute(Ak_Locator_Model_Location::ENTITY, $code);
    $attribute->setData('used_in_forms', array('location_edit','location_create'));
    $attribute->save();
}

$installer->endSetup();
