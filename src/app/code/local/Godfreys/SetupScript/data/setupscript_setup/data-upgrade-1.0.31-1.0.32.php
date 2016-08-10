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
    // Footer Links Page | Godfreys
    //==========================================================================
    $blockTitle = "Footer Links";
    $blockIdentifier = "footer-links";
    $blockIsActive = 1;
    $blockStores = array(2);
    $blockContent = <<<EOD
        	<div class="nav-col nav-col-1">
	<h4>Vacuum Cleaners</h4>
		<ul>
		<li><a href="/vacuum-cleaners/vacuum-cleaner-types/bagless">Bagless</a></li>
		<li><a href="/vacuum-cleaners/vacuum-cleaner-types/bagged">Bagged</a></li>
		<li><a href="/vacuum-cleaners/vacuum-cleaner-types/upright">Upright</a></li>
		<li><a href="/vacuum-cleaners/vacuum-cleaner-types/barrel-canister">Barrel / Canister</a></li>
		<li><a href="/vacuum-cleaners/vacuum-cleaner-types/wet-dry">Wet &amp; Dry</a></li>
		<li><a href="/vacuum-cleaners/vacuum-cleaner-types/stick-vacuums">Stick Vacuums</a></li>
		<li><a href="/vacuum-cleaners/vacuum-cleaner-types/handheld-vacs">Handheld Vacs</a></li>
		<li><a href="/vacuum-cleaners/vacuum-cleaner-types/compact-light-weight">Compact / Lightweight</a></li>
		<li><a href="/vacuum-cleaners/speciality-vacuum-cleaners/anti-allergy">Anti-Allergy</a></li>
		<li><a href="/vacuum-cleaners/speciality-vacuum-cleaners/asthma">Asthma</a></li>
		<li><a href="/vacuum-cleaners/speciality-vacuum-cleaners/pet-hair">Pet Hair</a></li>
		<li><a href="/commercial-vacuums">Commercial Vacuums</a></li>
		<li><a href="/vacuum-cleaners/ducted-vacuums">Ducted Vacuums</a></li>
		</ul>
	</div>
	<div class="nav-col nav-col-2">
		<h4>Steam &amp; Shampoo</h4>
		<ul>
		<li><a href="/steam-shampoo/steam-mops">Steam Mops</a></li>
		<li><a href="/steam-shampoo/steam-cleaners">Steam Cleaners</a></li>
		<li><a href="/steam-shampoo/carpet-shampooers">Carpet Shampooers</a></li>
		</ul>
		<h4 class="desktop"><a style="text-decoration: none;" href="/vacuum-bags-parts-accessories/accessory-finder-1">Accessory Finder</a></h4>
	</div>
	<div class=" nav-col nav-col-3">
		<h4>About the Site</h4>
		<ul>
		<li><a href="/delivery-information">Delivery Information</a></li>
		<li><a title="Trade-in offers on vacuum cleaners" href="/trade-ins" target="_self">Trade-In Discounts</a></li>
		<li><a href="/catalog/seo_sitemap/category/">Site Map</a></li>
		<li><a title="Vacuum Cleaner Repairs &amp; Servicing" href="/vacuum-servicing-repairs">Vacuum Repairs &amp; Service</a></li>
		<li><a href="/exchange-returns">Exchanges &amp; Returns</a></li>
		<li><a href="http://www.godfreys.com.au/godfreys-blog/blog-overview">Godfreys Blog</a></li>
		<li><a title="Suppliers to the Godfreys Website" href="/our-suppliers" target="_self">Our Suppliers</a></li>
		<li><a href="/national-asthma-council" target="_self">National Asthma Council</a></li>
		<li><a href="/privacy-policy" target="_self">Privacy Policy</a></li>
		<li><a href="/terms-and-conditions">Terms &amp; Conditions</a></li>
		</ul>
	</div>
	<div class="nav-col nav-col-4">
		<h4>About US</h4>
		<ul>
		<li><a href="/about-us " target="_self">Company&nbsp;History</a></li>
		</ul>
	</div>

<div class="nav-col bx-support nav-col-5">
	<h4>Support</h4>
	<ul>
	    <li><a href="/support/contact-us">Contact Us</a></li>
        <li><a href="/locator">Find a Store</a></li>
        <li><a href="/support/faq" target="_self">FAQ</a></li>
        <li><a href="/warranty/registration/" target="_self">Register Your Warranty</a></li>
        <li><a href="/support/asthma-allergy-sufferers">Asthma &amp; Allergy Sufferers</a></li>
        <li><a href="/support/pet-owners">Pet Owners</a></li>
	</ul>
</div>
EOD;

    $block = Mage::getModel('cms/block')->getCollection()
        ->addStoreFilter(2, $withAdmin = false)
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

    //==========================================================================
    //==========================================================================
    //==========================================================================


    $installer->endSetup();
} catch (Excpetion $e) {
    Mage::logException($e);
    Mage::log("ERROR IN SETUP " . $e->getMessage());
}