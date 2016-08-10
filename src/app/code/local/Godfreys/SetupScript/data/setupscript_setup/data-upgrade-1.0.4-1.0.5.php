<?php

try {

    $installer = $this;
    $installer->startSetup();

    // Force the store to be admin
    Mage::app()->setUpdateMode(false);
    Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
    if (!Mage::registry('isSecureArea'))
        Mage::register('isSecureArea', 1);

    //==========================================================================
    // Block Header Top Left static Block | Godfreys
    //==========================================================================
    $blockTitle = "Block Header Top Left";
    $blockIdentifier = "block_header_top_left";
    $blockStores = array(0);
    $blockIsActive = 1;
    $blockContent = <<<EOD
        {{block type="core/template" block_id="top-account" template="customer/account/top.phtml"}}
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
    //==========================================================================
    //==========================================================================
    //==========================================================================

    //==========================================================================
    // Block Header Top Right static Block | Godfreys
    //==========================================================================
    $blockTitle = "Block Header Top Right";
    $blockIdentifier = "block_header_top_right";
    $blockStores = array(0);
    $blockIsActive = 1;
    $blockContent = <<<EOD
        <div class="compare"><a href="javascript:void(0)" onclick="popWin('{{store url=""}}catalog/product_compare/index/','compare','top:0,left:0,width=820,height=600,resizable=yes,scrollbars=yes')">Compare</a></div>
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
    //==========================================================================
    //==========================================================================
    //==========================================================================

    $installer->endSetup();
} catch (Excpetion $e) {
    Mage::logException($e);
    Mage::log("ERROR IN SETUP " . $e->getMessage());
}