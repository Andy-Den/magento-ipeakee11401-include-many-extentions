<?php

try {

    $installer = $this;
    $installer->startSetup();

    // block find_a_store_lhs_2 | locator detail
    Mage::app()->setUpdateMode(false);
    Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
    if (!Mage::registry('isSecureArea'))
        Mage::register('isSecureArea', 1);

    //==========================================================================
    // // block find_a_store_lhs_2 | locator detail page | Godfreys
    //==========================================================================
    $blockTitle = "find a store lhs 2";
    $blockIdentifier = "find_a_store_lhs_2";
    $blockStores = array(0);
    $blockIsActive = 1;
    $blockContent = <<<EOD
        <p><img src="/skin/frontend/ultimo/default/images/find_a_store_lhs_2.jpg" alt="" /></p>
EOD;
    $block = Mage::getModel('cms/block')->load($blockIdentifier);
    if ($block->getId() == 0)
    {
        $block = Mage::getModel('cms/block');
    }
    else
    {
        // if exists then delete
        $block->delete();
        $block = Mage::getModel('cms/block');
    }
    $block->setTitle($blockTitle);
    $block->setIdentifier($blockIdentifier);
    $block->setStores($blockStores);
    $block->setIsActive($blockIsActive);
    $block->setContent($blockContent);
    $block->save();

    $installer->endSetup();
} catch (Excpetion $e) {
    Mage::logException($e);
    Mage::log("ERROR IN SETUP " . $e->getMessage());
}