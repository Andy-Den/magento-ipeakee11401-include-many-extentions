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
    // Home Rhs 2 | Godfreys
    //==========================================================================
    $blockTitle = "Home Rhs 2";
    $blockIdentifier = "home_rhs_2";
    $blockStores = array(0);
    $blockIsActive = 1;
    $blockContent = <<<EOD
        <div id="home-rhs-2">
            <a href="{{store url=''}}delivery-information"><span class="free-shipping">&nbsp;</span>Free Shipping Over $99</a>
            <a href="{{store url=''}}safe-secure-shopping"><span class="safe-secure-shopping">&nbsp;</span>Safe, Secure Shopping</a>
            <a href="{{store url=''}}store-locator"><span class="find-a-store">&nbsp;</span>Find a store</a>
        </div>
EOD;
    $block = Mage::getModel('cms/block')->load($blockIdentifier);
    if ($block->getId() == 0) {
        $block = Mage::getModel('cms/block');
    } else { // if exists then delete
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
	 