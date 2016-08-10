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
		<li><a href="/vacuum-cleaners/commercial-vacuum-cleaners">Commercial Vacuums</a></li>
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
        <li><a href="/store-locator">Find a Store</a></li>
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
    $pageTitle = "Privacy Policy";
    $pageIdentifier = "privacy-policy";
    $pageStores = array(0);
    $pageIsActive = 1;
    $pageUnderVersionControl = 0;
    $pageContentHeading = "";
    $pageContent = <<<EOD
<h2><span style="color: #000000; font-size: medium;"><strong>GODFREYS PRIVACY POLICY</strong></span></h2>
<p>&nbsp;</p>
<p><span style="color: #000000; font-size: small;">Godfreys complies with the State and Commonwealth Privacy Laws including the <em>Privacy Act 1988 (Cth) </em>and the Australian Privacy Principles (�Privacy Law�).&nbsp; Godfreys is committed to your privacy and to continue providing financial services in a confidential and safe manner.</span></p>
<p><span style="font-size: small;"><span style="color: #000000;">This Privacy Policy summarises how Godfreys handles your personal information.&nbsp;</span><span style="color: #000000;">By choosing to become a customer of Godfreys, you can be assured that all personal and sensitive information you provide to Godfreys will be respected and kept secure in accordance with Privacy Law and this Privacy Policy.&nbsp; By engaging with Godfreys you acknowledge your acceptance of this Privacy Policy.</span></span></p>
<p>&nbsp;</p>
<p><span style="color: #000000; font-size: small;"><strong><span style="text-decoration: underline;">Information Godfreys collects and holds</span></strong></span></p>
<p><span style="color: #000000; text-decoration: underline; font-size: small;">Definition of Personal Information</span></p>
<p><span style="color: #000000; font-size: small;">Personal information is defined by the </span><em style="color: #000000; font-size: small;">Privacy Act 1988 (Cth)</em><span style="color: #000000; font-size: small;"> as </span><em style="color: #000000; font-size: small;">�</em><em style="color: #000000; font-size: small;">information or an opinion about an identified individual, or an individual who is reasonably identifiable: (a) whether the information or opinion is true or not; and (b) whether the information or opinion is recorded in a material form or not.�</em></p>
<p><span style="color: #000000; font-size: small;">From time to time, Godfreys may collect certain of your personal information only in connection with the purpose for which it was collected as being reasonably necessary for or related to Godfreys�s business.&nbsp; The kind of information we collect will depend on your relationship with Godfreys (e.g. as a customer, business partner, employee or franchisee.&nbsp; Generally, the only personal information Godfreys collect about you is that which you choose to tell us or which you authorise Godfreys to obtain.</span></p>
<p><span style="color: #000000; font-size: small;">The type of information Godfreys collect may include</span></p>
<ul>
<li><span style="color: #000000; font-size: small;"><span style="text-decoration: underline;">Customers:</span> your name, address, telephone number and billing information.</span></li>
<li><span style="color: #000000; font-size: small;"><span style="text-decoration: underline;">Franchisees/Potential employees/Contractors:</span> your name, address, telephone number, tax file number, tax residency status, current assets, current loans and other encumbrances, employment history, police history (if any) and billing information.</span></li>
</ul>
<p>&nbsp;</p>
<p><span style="text-decoration: underline; color: #000000; font-size: small;">Definition of Sensitive Information</span></p>
<p><span style="color: #000000; font-size: small;">Sensitive information is a special category of the most sensitive personal information including racial or ethnic origin, political opinion etc.&nbsp; Godfreys does not collect any of your sensitive information.</span></p>
<p>&nbsp;</p>
<p><span style="color: #000000; font-size: small;"><strong><span style="text-decoration: underline;">How Godfreys collect and hold your personal information</span></strong></span></p>
<p><span style="color: #000000; font-size: small;">Where possible, Godfreys will collect your personal information directly from you.</span></p>
<p><span style="color: #000000; font-size: small;">Personal and sensitive information may be collected from you when you provide it to Godfreys directly.</span></p>
<p><span style="color: #000000; font-size: small;">From time to time, with your consent, Godfreys may also collect personal information from third parties including:</span></p>
<ul>
<li><span style="color: #000000; font-size: small;">credit reporting bodies if Godfreys request a report about your credit history</span></li>
<li><span style="color: #000000; font-size: small;">other credit providers if Godfreys request information from them about the products they provide to you</span></li>
<li><span style="color: #000000; font-size: small;">organisations that Godfreys has an arrangement with to jointly offer products and/or an alliance with to share information for marketing purposes to provide you with products or services and/or to promote a product or service</span></li>
<li><span style="color: #000000; font-size: small;">marketing companies (if Godfreys acquire contact information to tell people about Godfreys products and services that may interest them); and</span></li>
<li><span style="color: #000000; font-size: small;">brokers and other parties who may have introduced you to Godfreys</span></li>
</ul>
<p>&nbsp;</p>
<p><span style="color: #000000; font-size: small;">Godfreys has established appropriate physical, electronic and managerial procedures to safeguard any information Godfreys collect. This helps prevent unauthorised access, maintains data accuracy and ensures that the information is used correctly.</span></p>
<p><span style="color: #000000; font-size: small;">All data transferred to and from the Godfreys servers is encrypted and a firewall is in place to prevent intrusion.&nbsp; All data stored within the Godfreys�s systems is designed to only be able to be accessed by authorised staff members and the hosting facility.</span></p>
<p>&nbsp;</p>
<p><span style="color: #000000; font-size: small;"><strong><span style="text-decoration: underline;">The purpose for which Godfreys collect, hold, use and disclose personal information. </span></strong></span></p>
<p>&nbsp;</p>
<p><span style="color: #000000; font-size: small;">&nbsp;Godfreys collect personal information that Godfreys consider relevant, and which is outlined in your written authority, for the purpose of providing Godfreys�s services.&nbsp; Sensitive information, in most cases, can only be disclosed with your written consent. Any personal information collected about an individual will not be used or disclosed for the purposes of direct marketing unless the individual has given Godfreys consent to do so. Any personal information collected about an individual will not be disclosed to any overseas recipients, unless the individual has given Godfreys consent to do so.</span></p>
<p><span style="color: #000000; font-size: small;">Some of the ways Godfreys use personal information include to:</span></p>
<p><span style="color: #000000; font-size: small; text-decoration: underline;">Customers:</span></p>
<ul>
<li><span style="color: #000000; font-size: small;">personalise your shopping experience</span></li>
<li><span style="color: #000000; font-size: small;">communicate with you and others as part of Godfreys�s core business</span></li>
<li><span style="color: #000000; font-size: small;">send you information regarding changes to Godfreys�s policies, other terms and conditions, on-line Services and other administrative issues</span></li>
<li><span style="color: #000000; font-size: small;">enable Godfreys to provide a product or service</span></li>
<li><span style="color: #000000; font-size: small;">manage accounts and perform other administrative and operational tasks (including risk&nbsp;</span><span style="color: #000000; font-size: small;">management, systems development and testing, credit scoring and staff training, collecting debts&nbsp;</span><span style="color: #000000; font-size: small;">and market or customer satisfaction research)</span></li>
<li><span style="color: #000000; font-size: small;">allow Godfreys to track your order history</span></li>
<li><span style="color: #000000; font-size: small;">prevent, detect and investigate crime, including fraud and money laundering, and analyse and manage other commercial risks</span></li>
<li><span style="color: #000000; font-size: small;">verify information you have given to Godfreys&nbsp; </span></li>
<li><span style="color: #000000; font-size: small;">carry out market research and analysis, including satisfaction surveys</span></li>
<li><span style="color: #000000; font-size: small;">provide marketing information to you (including information about other products and services offered by selected third party partners) in preferences you have expressed</span></li>
<li><span style="color: #000000; font-size: small;">manage Godfreys�s infrastructure and business operations and comply with internal policies and procedures, including those relating to auditing accounting billing and collections IT systems data and website hosting business continuity and records, document and print management</span></li>
<li><span style="color: #000000; font-size: small;">resolve complaints, and handle requests for data access or correction</span></li>
<li><span style="color: #000000; font-size: small;">comply with applicable laws and regulatory obligations (including laws outside your country of residence), such as those relating to anti-money laundering, sanctions and anti-terrorism</span></li>
<li><span style="color: #000000; font-size: small;">comply with legal process and respond to requests from public and governmental authorities (in outside your country of residence)</span></li>
<li><span style="color: #000000; font-size: small;">establish and defend legal rights protect Godfreys�s operations or those of any of Godfreys�s group companies or insurance business partners, Godfreys�s rights or property, and/or that of Godfreys�s group companies, you or others and pursue available remedies or limit Godfreys�s damages</span></li>
</ul>
<p><span style="color: #000000; font-size: small; text-decoration: underline;">Franchisees/Potential employees/Contractors:</span></p>
<ul>
<li><span style="color: #000000; font-size: small;">all of the above</span></li>
<li><span style="color: #000000; font-size: small;">assess your current or past financial/credit position</span></li>
<li><span style="color: #000000; font-size: small;">assess your suitability and continued suitability for employment/franchise-ownership</span></li>
</ul>
<p>&nbsp;</p>
<p><span style="color: #000000; font-size: small;"><strong><span style="text-decoration: underline;">What happens if you don't provide all this information?</span></strong></span></p>
<p><span style="color: #000000; font-size: small;"><strong>&nbsp;</strong></span><span style="color: #000000; font-size: small;">If you do not provide some or all of the personal information requested, Godfreys may not be able to provide you with the benefit of Godfreys�s services.</span></p>
<p>&nbsp;</p>
<p><span style="color: #000000; font-size: small;"><strong><span style="text-decoration: underline;">Using a pseudonym or engaging with Godfreys anonymously</span></strong></span></p>
<p><span style="color: #000000; font-size: small;"><strong>&nbsp;</strong></span><span style="color: #000000; font-size: small;">Where practicable, you will be given the opportunity to engage with Godfreys on an anonymous basis or using a pseudonym.&nbsp; Due to the nature of Godfreys�s services, in most cases, anonymity will not be possible.</span></p>
<p>&nbsp;</p>
<p><span style="color: #000000; font-size: small;"><strong><span style="text-decoration: underline;">To whom does Godfreys disclose your personal information?</span></strong></span></p>
<p><span style="color: #000000; font-size: small;">&nbsp;Godfreys may disclose your personal information to:</span></p>
<ul>
<li><span style="color: #000000; font-size: small;">credit agencies</span></li>
<li><span style="color: #000000; font-size: small;">government authorities (where required by law)</span></li>
<li><span style="color: #000000; font-size: small;">third parties involved in court action (where required by law)</span></li>
<li><span style="color: #000000; font-size: small;">other parties that provide support services to Godfreys�s including support merchant services, online sales and marketing programs</span></li>
<li><span style="color: #000000; font-size: small;">professional advisers</span></li>
<li><span style="color: #000000; font-size: small;">potential business partners or purchasers</span></li>
</ul>
<p>&nbsp;</p>
<p><span style="color: #000000; font-size: small;"><strong><span style="text-decoration: underline;">Credit Card Details</span></strong></span></p>
<p><span style="color: #000000; font-size: small;">Godfreys does not store credit card numbers in Godfreys�s system. Your credit card details will be passed to the payment gateway as soon as they have been collected.</span></p>
<p>&nbsp;</p>
<p><strong style="color: #000000; font-size: small;"><span style="text-decoration: underline;">Reviews and Ratings</span></strong></p>
<p><span style="color: #000000; font-size: small;">&nbsp;Godfreys encourage you as a customer to leave product and service reviews on Godfreys�s site to help other customers make purchase decisions. Godfreys reserves the right to publish all or part of any review or rating that is submitted to the site, including the customer's first name and suburb.</span></p>
<p>&nbsp;</p>
<p><span style="color: #000000; font-size: small;"><strong><span style="text-decoration: underline;">Website cookies and usage information</span></strong></span></p>
<p><span style="color: #000000; font-size: small;">When you access Godfreys�s website, Godfreys may use software embedded in Godfreys�s website (such as Javascript) and Godfreys may place small data files (or cookies) on your computer or other device to collect information about which pages you view and how you reach them, what you do when you visit a page, the length of time you remain on the page, and how Godfreys perform in providing content to you. A cookie does not identify individuals personally, but it does identify computers.</span></p>
<p>&nbsp;</p>
<p><span style="color: #000000; font-size: small;">You can set your browser to notify you when you receive a cookie and this will provide you with an opportunity to either accept or reject it in each instance. &nbsp;Godfreys may gather your IP address as part of Godfreys�s business activities and to assist with any operational difficulties or support issues with Godfreys�s services.&nbsp; This information does not identify you personally.</span></p>
<p>&nbsp;</p>
<p><span style="color: #000000; font-size: small;"><strong><span style="text-decoration: underline;">External Links</span></strong></span></p>
<p><span style="color: #000000; font-size: small;">Godfreys' website may contain links to other websites. When you access these links Godfreys recommend that you read the website owner's privacy statement before disclosing your personal information. Godfreys does not accept responsibility for inappropriate use, collection, storage or disclosure of your personal information collected outside Godfreys�s website.</span></p>
<p>&nbsp;</p>
<p><span style="color: #000000; font-size: small;"><strong><span style="text-decoration: underline;">Opting out of direct marketing communications</span></strong></span></p>
<p><span style="color: #000000; font-size: small;"><strong>&nbsp;</strong></span><span style="color: #000000; font-size: small;">Where Godfreys use your personal information to send you marketing and promotional information by post, email or telephone, Godfreys will provide you with an opportunity to opt-out of receiving such information. By electing not to opt-out, Godfreys will assume Godfreys have your implied consent to receive similar information and communications in the future. &nbsp;Godfreys will always ensure that Godfreys�s opt-out notices are clear, conspicuous and easy to take up.&nbsp; If you wish to opt out of communications from Godfreys, please use the contact details below.</span></p>
<p>&nbsp;</p>
<p><span style="color: #000000; font-size: small;"><strong><span style="text-decoration: underline;">Cross-border disclosures of your personal information</span></strong></span></p>
<p><span style="color: #000000; font-size: small;">Godfreys uses data hosting facilities and third party service providers to assist Godfreys with providing our goods and services.&nbsp; As a result, your personal information may be transferred to, and stored at, a destination outside Australia.</span></p>
<p><span style="color: #000000; font-size: small;">Part or all of Godfreys�s website and/or data facilities may be administered and hosted by off-shore service providers.</span></p>
<p><span style="color: #000000; font-size: small;">Whilst Godfreys takes reasonable steps to ensure that off-shore service providers comply with Privacy Law. Godfreys does not take any responsibility for the manner in which Google stores or uses your personal information.</span></p>
<p>&nbsp;</p>
<p><span style="color: #000000; font-size: small;"><strong><span style="text-decoration: underline;">How an individual access their personal information held by Godfreys</span></strong></span></p>
<p><span style="color: #000000; font-size: small;"><strong>&nbsp;</strong></span><span style="color: #000000; font-size: small;">If an individual wishes to exercise their rights of access or alternatively has any questions or believes that any personal information held by Godfreys is incorrect or incomplete, the individual can write to Godfreys�s Privacy Officer at the address below.</span></p>
<p><span style="color: #000000; font-size: small;">Godfreys will then take all reasonable steps to correct the information or if necessary discuss alternative actions with the individual.</span></p>
<p><span style="color: #000000; font-size: small;">Personal information will only be released to the individual directly, unless Godfreys are provided with a written, signed authority by individual to provide it to a third party.</span></p>
<p>&nbsp;</p>
<p><span style="color: #000000; font-size: small;"><strong><span style="text-decoration: underline;">Updating your personal information</span></strong></span></p>
<p><span style="color: #000000; font-size: small;"><strong>&nbsp;</strong></span><span style="color: #000000; font-size: small;">You may ask Godfreys to update, correct or delete the personal information Godfreys hold about you at any time. Godfreys will take reasonable steps to verify your identity before granting access or making any corrections to or deletion of your information. Godfreys also has obligations to take reasonable steps to correct personal information Godfreys holds when Godfreys is satisfied that it is inaccurate, out- of-date, incomplete, irrelevant or misleading for the purpose for which it is held.&nbsp; If you wish to update your personal information, please use the contact details below.</span></p>
<p>&nbsp;</p>
<p><span style="color: #000000; font-size: small;"><strong><span style="text-decoration: underline;">Policy Changes</span></strong></span></p>
<p><span style="color: #000000; font-size: small;">Godfreys may revise this Privacy Policy from time to time by updating this page. The revised Privacy Policy will take effect when it is posted on Godfreys�s website. &nbsp;Godfreys suggests you review Godfreys�s Privacy Policy regularly.</span></p>
<p>&nbsp;</p>
<p><span style="color: #000000; font-size: small;"><strong><span style="text-decoration: underline;">How to contact Godfreys regarding privacy</span></strong></span></p>
<p><span style="color: #000000; font-size: small;"><strong>&nbsp;</strong></span><span style="color: #000000; font-size: small;">If an individual would like to make further inquiries or complain about a breach of the Australian Privacy Principles, or complain about a registered Australian Privacy Principles code (if any) that may relate Godfreys�s business, the individual can contact Godfreys�s Privacy Officer at the address:</span></p>
<p>&nbsp;</p>
<p><span style="color: #000000; font-size: small;"><em>Privacy Officer</em></span></p>
<p><span style="color: #000000; font-size: small;"><em>Godfreys</em></span></p>
<p><span style="color: #000000; font-size: small;"><em>Building 2, Brandon Business Park, 530, Springvale Road</em></span></p>
<p><span style="color: #000000; font-size: small;"><em>Glen Waverley, Victoria, </em><em>Australia</em></span></p>
<p>&nbsp;</p>
<p><span style="color: #000000; font-size: small;">Godfreys will take all complaints regarding privacy of information seriously. Godfreys will respond to any requests, questions, or complaints as soon as possible in a reasonable time frame.</span></p>
<p>&nbsp;</p>
<p><span style="color: #000000; font-size: small;">In this document, �Godfreys� means International Cleaning Solutions Pty Ltd ACN 119 462 798, International Cleaning Solutions Group Pty Limited ACN 120 157 191, Electrical Home-Aids Pty Ltd ACN 007 539 577 and any related body corporates within the meaning of the <em>Corporations Act 2001 (Cth)</em>.</span></p>
<p>&nbsp;</p>

EOD;

    $pageRootTemplate = 'one_column';
    $pageLayoutUpdateXml = <<<EOD
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
        $page->delete();
        $page = Mage::getModel('cms/page');
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

