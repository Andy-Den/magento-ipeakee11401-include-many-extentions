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
    $blockTitle = "Footer Contact";
    $blockIdentifier = "footer_contact";
    $blockStores = array(0);
    $blockIsActive = 1;
    $blockContent = <<<EOD
        <div id="apDivContactUsFooterBarBox1"><span class="vp1"><b>Contact Us</b></span></div>
        <div id="apDivContactUsFooterBarTelephone"></div>
        <div id="apDivContactUsFooterBarNumber"><span class="FooterTelephoneNumber">012346789</span></div>
        <div id="apDivContactUsFooterBarOpeningTimes"><span class="vp1"> Mon to Fri, 9am - 5pm</span></div>
        <div id="apDivContactUsFooterBarYoutube"><a href="http://www.youtube.com/vorwerkkobold" target="_blank"><img src="{{config path="web/unsecure/base_url"}}skin/frontend/enterprise/vorwerk/images/vorwerk/youtube_footer.png" border="0"/></a></div>
        <div id="apDivContactUsFooterBarFacebook"><a href="https://www.facebook.com/Vorwerk.Saugroboter" target="_blank"><img src="{{config path="web/unsecure/base_url"}}skin/frontend/enterprise/vorwerk/images/vorwerk/facebook.png" border="0"/></a></div>
        <div id="apDivContactUsLinks"><a href="#" class="link-footer">Write to us</a><br/><a href="#" class="link-footer">Book a Demonstration</a></div>
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
