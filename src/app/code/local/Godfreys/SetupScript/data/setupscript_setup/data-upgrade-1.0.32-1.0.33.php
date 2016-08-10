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
    // Footer Payment Methods static Block | Godfreys
    //==========================================================================
    $blockTitle = "Block Footer Links";
    $blockIdentifier = "block_footer_links";
    $blockStores = array(Mage::app()->getStore()->getStoreId());
    $blockIsActive = 1;
    $blockContent = <<<EOD
<div class="grid12-4 no-left-gutter newletters">
					{{block type="newsletter/subscribe" template="newsletter/footer_subscribe.phtml"}}
				</div>
				<div class="grid12-3 locator">
					<label>Locate a Store</label>
					<form method="get" action="/locator/search/" name="store_locator_form" class="footer-form" id="store_locator_form">
					    <select id="loc-search-country" class="loc-srch-country" name="country" style="display:none">
                            <option selected="selected" value="Australia">Australia</option>
                        </select>
						<input type="text" onblur="if (this.value=='') this.value = this.defaultValue" onfocus="if (this.value==this.defaultValue) this.value = ''" value="Enter postcode" name="s" class="input-text">
						<input type="hidden" value="250" name="distance">
						<button type="submit" class="button"><span><span>Locate</span></span></button>
					</form>
				</div>
				<div class="social-payment">
					<div class="footer-social">{{block type="cms/block" block_id="footer_socialsharing"}}</div>
					<div class="footer-securely">{{block type="cms/block" block_id="footer_paymentmethods"}}</div>
				</div>
<script type="text/javascript">
	jQuery( "#store_locator_form" ).submit(function( event ) {
        if(jQuery('#store_locator_form input[name="s"]').val() == 'Enter postcode'){jQuery('#store_locator_form input[name="s"]').val('');}
});
</script>
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