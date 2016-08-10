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
    // Footer Links Page | Godfreys
    //==========================================================================
    $blockTitle = "Block Header Top Left";
    $blockIdentifier = "block_header_top_left";
    $blockIsActive = 1;
    $blockStores = array(Mage::app()->getStore()->getStoreId());
    $blockContent = <<<EOD
        	<a id="mobile-menu" class="mobile" href="Javascript:void(0)">Menu</a>
{{block type="core/template" block_id="top-account" template="customer/account/top.phtml"}}
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
    // Block Header Top Right Page | Godfreys
    //==========================================================================
    $blockTitle = "Block Header Top Right";
    $blockIdentifier = "block_header_top_right";
    $blockIsActive = 1;
    $blockStores = array(Mage::app()->getStore()->getStoreId());
    $blockContent = <<<EOD
        	<div class="compare desktop"><a href="javascript:void(0)" onclick="popWin('{{store url=""}}catalog/product_compare/index/','compare','top:0,left:0,width=750,height=600,resizable=yes,scrollbars=yes')">Compare ({{block type="core/template" template="catalog/product/compare/count.phtml"}})</a></div>
<a href="{{store url=""}}checkout/cart/" class="mobile minicart-icon">Cart</a>
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

