<?php

$this->startSetup();


$productEntityTypeId = Mage::getModel('eav/entity_type')->loadByCode(Mage_Catalog_Model_Product::ENTITY)->getId();
$attributeCodes = array(
    'shoutout',
    'warranty',
    'anti_allergy',
    'asthma',
    'pet_hair',
    'level_of_clean',
    'online_only',
);


foreach ($attributeCodes as $attributeCode) {
    $attribute = Mage::getModel('eav/entity_attribute')->loadByCode($productEntityTypeId, $attributeCode);
    if ($attribute) {
        $attribute->setUsedInProductListing(1)->save();
    }
}


$this->endSetup();
