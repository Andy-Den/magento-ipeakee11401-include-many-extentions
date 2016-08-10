<?php
/*
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Balance
 * @package    Feefocache
 * @copyright  Copyright (c) 2011 Balance
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */


$installer = $this;
$setup = new Mage_Catalog_Model_Resource_Setup('core_setup');
$installer->startSetup();

$setup->addAttribute('catalog_product', 'feefo_reviews_average', array(
    'group' => 'FeeFo Reviews',
    'input' => 'text',
    'type' => 'text',
    'label' => 'FeeFo Reviews Average',
    'backend' => '',
    'visible' => 1,
    'required' => 0,
    'user_defined' => 1,
    'searchable' => 0,
    'filterable' => 0,
    'comparable' => 0,
    'visible_on_front' => 0,
    'visible_in_advanced_search' => 0,
    'is_html_allowed_on_front' => 0,
    'is_configurable' => 1,
    'used_in_product_listing' => 1,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
));

$setup->addAttribute('catalog_product', 'feefo_reviews_count', array(
    'group' => 'FeeFo Reviews',
    'input' => 'text',
    'type' => 'text',
    'label' => 'FeeFo Reviews Count',
    'backend' => '',
    'visible' => 1,
    'required' => 0,
    'user_defined' => 1,
    'searchable' => 0,
    'filterable' => 0,
    'comparable' => 0,
    'visible_on_front' => 0,
    'visible_in_advanced_search' => 0,
    'is_html_allowed_on_front' => 0,
    'is_configurable' => 1,
    'used_in_product_listing' => 1,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
));

$installer->endSetup();
