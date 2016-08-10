<?php

try {

    // file data-upgrade-1.0.3-1.0.4.php

    $installer = $this;
    $setup = new Mage_Eav_Model_Entity_Setup('core_setup');
    $installer->startSetup();
    /**
     * Adding The Expert Says Attribute
     */

// the attribute added will be displayed under the group/tab FeeFo Reviews in product edit page
    $setup->addAttribute('catalog_product', 'expert_says', array(
        'group' => 'FeeFo Reviews',
        'input' => 'textarea',
        'type' => 'text',
        'label' => 'The Expert Says',
        'backend' => '',
        'visible' => 1,
        'required' => 0,
        'user_defined' => 1,
        'searchable' => 1,
        'filterable' => 0,
        'comparable' => 0,
        'visible_on_front' => 1,
        'visible_in_advanced_search' => 0,
        'is_html_allowed_on_front' => 0,
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    ));

    $installer->endSetup();
} catch (Excpetion $e) {
    Mage::logException($e);
    Mage::log("ERROR IN SETUP " . $e->getMessage());
}