<?php

try {
    $installer = $this;
    $installer->startSetup();

    // Force the store to be admin
    Mage::app()->setUpdateMode(false);
    //Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
    if (!Mage::registry('isSecureArea'))
        Mage::register('isSecureArea', 1);

    //==========================================================================
    // Header uvp links Page | Godfreys
    //==========================================================================
    $blockTitle = "Header uvp links";
    $blockIdentifier = "header_uvp_links";
    $blockIsActive = 1;
    $blockStores = array(Mage::app()->getStore()->getStoreId());
    $blockContent = <<<EOD
<div id="header-uvp-links">
<a href="{{store url=''}}delivery-information" class="free-ship-icon desktop"><span>Free Shipping Over $99</span></a>
<a href="{{store url=''}}safe-secure-shopping" class="shopping-icon desktop "><span>Safe, Secure Shopping</span></a>
<a href="{{store url=''}}store-locator" class="find-store-icon desktop"><span>Find a store</span></a>
<a href="{{store url=''}}store-locator" class="find-store-icon mobile"><span>Find a store</span></a>
<a id="mobile-account-top" href="Javascript:void(0)" class="my-account-icon mobile"><span>My account</span></a>
</div>
EOD;

    $cmsBlock = array(
        'title'         => $blockTitle,
        'identifier'    => $blockIdentifier,
        'content'       => $blockContent,
        'is_active'     => 1,
        'stores'        => Mage::app()->getStore()->getStoreId()
    );

    $block = Mage::getModel('cms/block')->getCollection()
        ->addStoreFilter(Mage::app()->getStore()->getStoreId(), $withAdmin = true)
        ->addFieldToFilter('identifier', $blockIdentifier)
        ->getFirstItem()
        ;
    if ($block->getId() == 0)
    {
        $block = Mage::getModel('cms/block');
    }
    else
    {
        // if exists then delete
        $block = Mage::getModel('cms/block')->load($block->getId());
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

