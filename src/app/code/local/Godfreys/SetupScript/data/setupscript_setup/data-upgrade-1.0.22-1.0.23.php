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
    $blockTitle = "Footer Links";
    $blockIdentifier = "footer-links";
    $blockIsActive = 1;
    $blockStores = array(Mage::app()->getStore()->getStoreId());
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
		<h4>About</h4>
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

    //==========================================================================
    //==========================================================================
    //==========================================================================

//==========================================================================
    // 404 page not found ultimo Page | Godfreys
    //==========================================================================
    $pageTitle = "Pet Owners Cleaning Information";
    $pageIdentifier = "pet-owners";
    $pageStores = array(Mage::app()->getStore()->getStoreId());
    $pageIsActive = 1;
    $pageUnderVersionControl = 0;
    $pageContentHeading = "Cleaning Advice for Pet Owners";
    $pageContent = <<<EOD
<p><strong><br /></strong></p>
<p><span style="font-size: small;">We all love our pets. Whether you&rsquo;re a dog person or a cat person (or both!), our pets often become part of the family and they each have their own unique personality. &nbsp;</span></p>
<p><span style="font-size: small;">The downside of being a pet owner is that your pets can also make cleaning your house a nightmare! Luckily, the vacuum cleaner experts here at Godfreys have some handy advice for pet owners.</span></p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<h2><strong>Cats<img style="float: right;" title="Pet Owners - Cats" src="{{media url="wysiwyg/Cat_-_Pet_Owners.jpg"}}" alt="Pet Owners - Cats" width="300" /></strong></h2>
<p>C<span style="font-size: small;">ats have a tendency to curl up on furniture and leave behind fur which is often hard to remove, particularly on their favourite sleeping spots. Cats with strong personalities also tend to fight with each other and leave trails of fur across your carpet, which can be frustrating when it occurs on a daily basis.</span></p>
<p>&nbsp;</p>
<p><strong style="font-size: small;">Godfreys Recommends..</strong></p>
<p><span style="font-size: small;">If you own cats, find a vacuum cleaner with a turbo hand tool. This attachment can be used to easily remove cat hair from furniture and upholstery, and can also be used on rugs and carpet. &nbsp;</span></p>
<p>&nbsp;</p>
<p><span style="font-size: small;"><strong>Click here to view Godfreys range of <a href="/vacuum-cleaners/speciality/pet-hair-vacuum-cleaners" target="_self">cat vacuum cleaners</a>.</strong></span></p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<h2><strong>Dogs<img style="float: right;" title="Pet Owners - Dogs" src="{{media url="wysiwyg/Pet_Owners_-_Dog.jpg"}}" alt="Pet Owners - Dogs" width="250" /></strong></h2>
<p><span style="font-size: small;">Dogs can sleep anywhere at any time, and they tend to leave dog hair all over the place. They can bring dirt into the house and you also get the odd &lsquo;accident&rsquo; around the house, particularly with younger dogs.</span></p>
<p>&nbsp;</p>
<p><span style="font-size: small;"><strong>Godfreys Recommends...</strong></span></p>
<p><span style="font-size: small;">If you own dogs, find a vacuum cleaner that has pet accessories. These accessories can not only help to remove pet hair from your home, but some can also be used to groom your dog so they leave less hair lying around in the first place!</span></p>
<p><span style="font-size: small;"><br /></span></p>
<p><span style="font-size: small;">We also recommend both <a href="/steam-shampoo/steam-mops" target="_self">steam mops</a> and <a href="/steam-shampoo/carpet-shampooers" target="_self">carpet shampooers</a> for dog owners. This way if your dog leaves a mess, you can use the steam mop to loosen the tough stain, and then run the carpet shampooer over the area to remove all remaining mess from your carpets.&nbsp;</span></p>
<p><span style="font-size: small;"><br /></span></p>
<p><span style="font-size: small;">If stains are an ongoing problem with your dog, then you may need a carpet shampooer. Some carpet shampooers even have special &lsquo;spotlifters&rsquo; that can target stubborn stains on carpets and furniture.</span></p>
<p>&nbsp;</p>
<p><span style="font-size: small;"><strong><strong>Click here to view Godfreys range of <a href="/vacuum-cleaners/speciality/pet-hair-vacuum-cleaners" target="_self">dog vacuum&nbsp;cleaners</a>.</strong><br />&nbsp;</strong></span></p>
<p><strong><br /></strong></p>
<h2><strong>Pet Vacuums</strong></h2>
<p><span style="font-size: small;">If you&rsquo;re looking for a vacuum cleaner to help clean up after pets, then have a look at Godfreys range of pet vacuum cleaners. With a wide range of vacuums and carpet shampooers that are designed specifically to combat pet hair and stains, you&rsquo;re sure to find something to help you out!</span></p>
<p>&nbsp;</p>
<p><span style="font-size: small;"><strong><strong>Click here to view Godfreys range of <a href="/vacuum-cleaners/speciality/pet-hair-vacuum-cleaners" target="_self">pet vacuum cleaners</a>.</strong></strong></span></p>
EOD;

    $pageRootTemplate = 'two_columns_left';
    $pageLayoutUpdateXml = <<<EOD
    <reference name="left">
      <block type="reports/product_viewed" after="right.permanent.callout" name="right.reports.product.viewed" template="reports/product_viewed.phtml" />
            <block type="cms/block" name="free_machine_health_check">
                <action method="setBlockId"><block_id>free_machine_health_check</block_id></action>
            </block>
            <block type="cms/block" name="search_bag_accessories ">
                <action method="setBlockId"><block_id>search_bag_accessories </block_id></action>
            </block>
        </reference>
EOD;

    $pageCustomLayoutUpdateXML = <<<EOD
EOD;

    $pageMetaKeywords = "";
    $pageMetaDescription = "";
    $page = Mage::getModel('cms/page')->getCollection()
        ->addStoreFilter(Mage::app()->getStore()->getStoreId(), $withAdmin = true)
        ->addFieldToFilter('identifier', $pageIdentifier)
        ->getFirstItem()
    ;
    if ($page->getId() == 0) {
        $page = Mage::getModel('cms/page');
    }
    else{
        $page = Mage::getModel('cms/page')->load($page->getId());
    }
    $page->setTitle($pageTitle);
    $page->setIdentifier($pageIdentifier);
    $page->setStores($pageStores);
    $page->setIsActive($pageIsActive);
    $page->setUnderVersionControl($pageUnderVersionControl);
    $page->setContentHeading($pageContentHeading);
    $page->setContent($pageContent);
    $page->setRootTemplate($pageRootTemplate);
    $page->setLayoutUpdateXml($pageLayoutUpdateXml);
    $page->setCustomLayoutUpdateXml($pageCustomLayoutUpdateXML);
    $page->setMetaKeywords($pageMetaKeywords);
    $page->setMetaDescription($pageMetaDescription);
    $page->save();
    //==========================================================================
    //==========================================================================
    //==========================================================================

    $installer->endSetup();
} catch (Excpetion $e) {
    Mage::logException($e);
    Mage::log("ERROR IN SETUP " . $e->getMessage());
}

