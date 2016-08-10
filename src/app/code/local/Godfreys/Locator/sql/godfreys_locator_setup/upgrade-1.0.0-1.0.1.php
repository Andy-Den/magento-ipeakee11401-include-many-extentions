<?php
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->removeAttribute(Ak_Locator_Model_Location::ENTITY,'store_title');
$setup->removeAttribute(Ak_Locator_Model_Location::ENTITY,'store_address');

