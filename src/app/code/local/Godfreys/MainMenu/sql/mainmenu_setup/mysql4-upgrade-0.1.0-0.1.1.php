<?php
$installer = $this;
$installer->startSetup();

$installer->addAttribute('catalog_category', 'umm_cat_block_width', array(
    'group'				=> 'Menu',
    'label'				=> 'Content Width(px)',
    'note'				=> "This field is applicable only for top-level categories. If this field has not content, It will be set to auto.",
    'type'				=> 'text',
    'input'				=> 'text',
    'visible'			=> true,
    'required'			=> false,
    'backend'			=> '',
    'frontend'			=> '',
    'searchable'		=> false,
    'filterable'		=> false,
    'comparable'		=> false,
    'user_defined'		=> true,
    'visible_on_front'	=> true,
    'wysiwyg_enabled'	=> true,
    'is_html_allowed_on_front'	=> true,
    'global'			=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));
$installer->endSetup();