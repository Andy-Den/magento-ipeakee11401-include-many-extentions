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
    // Home Rhs 2 static Block | Godfreys
    //==========================================================================
    $blockTitle = "Home Rhs 2";
    $blockIdentifier = "home_rhs_2";
    $blockStores = array(0);
    $blockIsActive = 1;
    $blockContent = <<<EOD
        <div id="home-rhs-why">
<h2>why godfreys?</h2>
<div class="info-whygodfrey">
<p href="{{store url=''}}delivery-information">
    <span class="icon-ok">&nbsp;</span>Trusted as the vacuum and cleaning specialists for over 80 years
</p>
<p href="{{store url=''}}safe-secure-shopping">
    <span class="icon-ok">&nbsp;</span>Free delivery on all online orders over $99
</p>
<p href="{{store url=''}}store-locator">
    <span class="icon-ok">&nbsp;</span>Nationwide service and repair network
    </p>
</div>
</div>
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