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
    // Link Footer Checkout Page | Godfreys
    //==========================================================================
    $blockTitle = "Checkout Page - Link Footer";
    $blockIdentifier = "link_footer_checkout";
    $blockStores = array(0);
    $blockIsActive = 1;
    $blockContent = <<<EOD
        <div class="support-link">
			<h4>Support</h4>
			<ul>
			<li><a href="/contact-us">Contact Us</a></li>
			<li><a href="/store-locator">Find a Store</a></li>
			<li><a href="/faq" target="_self">FAQ</a></li>
			<li><a href="/warranty/registration/" target="_self">Register Your Warranty</a></li>
			<li><a href="/asthma-allergy-sufferers">Asthma &amp; Allergy Sufferers</a></li>
			<li><a href="/pet-owners">Pet Owners</a></li>
			</ul>
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

    //==========================================================================
    // Home Rhs 2 static Block | Godfreys
    //==========================================================================
    $blockTitle = "Checkout Page - Logo";
    $blockIdentifier = "logo_checkout_page";
    $blockStores = array(0);
    $blockIsActive = 1;
    $blockContent = <<<EOD
	<div class="checkout-logo">
		<ul>
			<li class="anz-logo"></li>
			<li class="norton-logo"></li>
		</ul>
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