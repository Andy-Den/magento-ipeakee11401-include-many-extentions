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
    // Frequently Asked Questions Page | Godfreys
    //==========================================================================
    $pageTitle = "Frequently Asked Questions";
    $pageIdentifier = "faq";
    $pageStores = array(2);
    $pageIsActive = 1;
    $pageUnderVersionControl = 0;
    $pageContentHeading = "Frequently Asked Questions";
    $pageContent = <<<EOD
       <p>&nbsp;</p>
<h3><strong>Where is my nearest Godfreys store?</strong></h3>
<p><span style="font-size: small;">You can find the Godfreys store closest to you by using our <a href="/store-locator" target="_self">Store Locator</a>.</span></p>
<p>&nbsp;</p>
<h3><strong>How long will my vacuum repair take?</strong></h3>
<p><span style="font-size: small;">Most vacuum cleaner repairs will be completed by Godfreys within 14 days. However, in situations where rare vacuum parts are required, please allow several more days for the parts to be sourced. Your Godfreys store will be able to advise you if this is going to be the case.</span></p>
<p>&nbsp;</p>
<h3><strong>Do you offer trade-ins on vacuum cleaners?</strong></h3>
<p><span style="font-size: small;">Yes, Godfreys do offer trade-ins on vacuums in working order! Bring your vacuum into your local Godfreys store and they will advise the amount they can offer.</span></p>
<p>&nbsp;</p>
<h3><strong>How do I purchase vacuum bags and parts for my vacuum cleaner?</strong></h3>
<p><span style="font-size: small;">You can purchase vacuum bags and spare parts for your vacuum cleaner online using our Accessory Finder. Alternatively, you can visit your nearest Godfreys store or call Godfreys Customer Service on 1800 815 270 to purchase vacuum bags and parts over the phone.</span></p>
<p>&nbsp;</p>
<h3><strong>My vacuum is losing suction, what should I do?</strong></h3>
<p><span style="font-size: small;">Have you cleaned your filters recently? Dirty and clogged filters are one of the main reasons for vacuum cleaners losing suction. If you own a bagged machine, have you changed the vacuum bag? If you have completed these steps and your vacuum is still losing suction, please take your vacuum cleaner to your nearest Godfreys store to have it assessed by an expert.</span></p>
<p>&nbsp;</p>
<h3><strong>Where can I take my vacuum cleaner for repair?</strong></h3>
<p><span style="font-size: small;">Take your vacuum cleaner to your nearest Godfreys store. You can find a store by using our <a href="/store-locator" target="_self">Store Locator</a>.</span></p>
<p>&nbsp;</p>
<h3><strong>What is your returns policy?</strong></h3>
<p><span style="font-size: small;">Godfreys offers a comprehensive returns policy. <a href="/exchange-returns" target="_self">You can view our Returns P</a><a href="/exchange-returns" target="_self">olicy here</a>.</span></p>
<p>&nbsp;</p>
<h3><strong>Do you supply loan machines for vacuum services and repairs?</strong></h3>
<p><span style="font-size: small;">We do not offer this service at present, however if this will be an issue please contact your local Godfreys store and they may be able to assist.</span></p>
<p>&nbsp;</p>
<h3><strong>Does my vacuum cleaner need to be serviced?</strong></h3>
<p><span style="font-size: small;">All vacuum manufacturers recommend an annual service for your vacuum cleaner. This is important, not only to actually clean and service your vacuum cleaner, but also to alert you to potential problems caused by overfilled bags, blocked filters or hoses, etc. With a serviced machine and a well maintained filtration system, you can expect a longer, more effective life from your vacuum cleaner.</span></p>
<p>&nbsp;</p>
<h3><strong>Can I use any type of paper or plastic bag in my vacuum cleaner?</strong></h3>
<p><span style="font-size: small;">No. Never use plastic bags in your vacuum cleaner. This has the effect of suffocating your machine from a cooling airflow and will almost certainly damage your vacuum cleaner. Use only proper vacuum cleaner bags made to fit your model. These bags are a special type of material that stop the dirt whilst allowing a good airflow through your machine. You can find the right bags for your machine using our <a href="/vacuum-cleaner-parts/accessories" target="_self">Accessory Finder</a>.&nbsp;</span></p>
<p>&nbsp;</p>
<h3><strong>Can my vacuum cleaner pick up water?</strong></h3>
<p><span style="font-size: small;">Most machines that have the ability to pick up water will clearly show this on the machine, such as "Wet &amp; dry vac", etc. Typically, wet &amp; dry machines are canister style with the motor that lifts off the top of the canister and some type of float mechanism under the motor (to stop excessive filling). If you are unsure, please contact us to verify this. If you use a dry-only machine to pick up water, it will almost certainly damage your machine and could be dangerous to you. If you need a vacuum to pick up water, <a href="/vacuum-cleaners/types/wet-dry" target="_self">take a look at our Wet &amp; Dry vacuums here</a>.&nbsp;</span></p>
<p>&nbsp;</p>
<h3><strong>I have a sore back. Is an upright vacuum cleaner better for me to clean my carpet?</strong></h3>
<p><span style="font-size: small;">It is a fairly common perception that an upright will be easier for people with a sore back. If you have a sore back, you definitely should look at a cleaning system with a rotating brush system - and whilst this does include uprights, it also includes barrel vacuum cleaners with a turbo or power head, which can be even lighter to operate. See our helpful section <a href="/upright-vs-barrel-vacuum-cleaners" target="_self">Upright Vs Barrel Vacuum Cleaners</a> for more information.</span></p>
<p>&nbsp;</p>
<h3><strong>Should I use my home vacuum cleaner at work?</strong></h3>
<p><span style="font-size: small;">Our experience shows that work vacuum cleaners can be used by a number of different people and, with all their best intentions, can be treated harshly. <a href="/vacuum-cleaners/commercial-vacuum-cleaners" target="_self">Commercial vacuum cleaners</a>, even the small ones, are built to be more robust and with less features than domestic vacuum cleaners. In the long run it could be better economy to supply your place of work with a vacuum cleaner more suited to the task.</span></p>
<p>&nbsp;</p>
<h3><strong>The motor on my vacuum cleaner sounds noisy. Should I get it serviced?</strong></h3>
<p><span style="font-size: small;">If your vacuum cleaner motor is too noisy, take it to your nearest Godfreys store and get a vacuum expert to take a look at it. Sometimes a service can help prolong the life of your vacuum cleaner.</span></p>
<p>&nbsp;</p>
<h3><strong>When should I change my vacuum cleaner bag?</strong></h3>
<p><span style="font-size: small;">Many vacuum cleaners have a dustbag full indicator. These can be used as a guide but are not foolproof. Periodically check the bag and ensure that it doesn't get too full.</span></p>
<p>&nbsp;</p>
<h3><strong>When should I change my vacuum filters?</strong></h3>
<p><span style="font-size: small;">Machines vary and manufacturer&rsquo;s instructions will differ from model to model. In general check your filters to ensure that they are not blocked with dirt. Some, not all, filters are washable and should be washed according to manufacturer&rsquo;s instructions. If filters are not maintained, the air that exits your machine will not be as clean and blockages may put extra stress on the vacuum cleaner motor and reduce its effective life.</span></p>
<p>&nbsp;</p>
<h3><strong>We are doing a renovation; can I use my vacuum cleaner to pick up plaster/cement dust?</strong></h3>
<p><span style="font-size: small;">The number one reason vacuums end up at our repair centre is blown motors is after picking up very fine plaster/cement dust. This type of dust is particularly messy and gets through filters. If it gets through the motor filter, particles can settle on the motor bearings and act as an abrasive to wear it down. Sometimes we see this dust clogging up the electrics of the motor causing short circuits. If you use your vacuum cleaner for this purpose, be aware of the above and show extra care at this time to maintain your machine. If you engage a builder or tradesman, ensure that you make a point of telling them upfront that your vacuum cleaner is not to be used and that they should bring their own to clean up when they have finished their work.</span></p>
<p>&nbsp;</p>
<h3><strong>Will my credit card details be safe if I buy online?</strong></h3>
<p><span style="font-size: small;">Your credit card details are very safe with us, as we never store your credit card details for any reason. You can safely shop online knowing that Godfreys use the trusted VeriSign security software, and we take customer data security very seriously.</span></p>
<p>&nbsp;</p>
<h3><strong>How long will it take my vacuum to get delivered?</strong></h3>
<p><span style="font-size: small;">Please see our section on <a href="/delivery-information" target="_self">Delivery Information</a> for all the details on shipping and delivery times.</span></p>
<p>&nbsp;</p>
<h3><strong>How do I get free delivery?</strong></h3>
<p><span style="font-size: small;">No matter where you live in Australia, you will receive <a href="/delivery-information" target="_self">free delivery</a> when you spend over $99 on the website. This includes both machines and accessories - i.e. if you spend over $99 on vacuum bags then you will receive free delivery.</span></p>
<p>&nbsp;</p>
<h3><strong>How do I choose between a bagless and bagged vacuum?</strong></h3>
<p><span style="font-size: small;">For information about the differences between bagged and bagless vacuums, please see our section on <a href="/floor-cleaning-tips/bagged-vs-bagless-vacuum-cleaners" target="_self">Bagged vs Bagless vacuum cleaners</a>.</span></p>
<p>&nbsp;</p>
<h3><strong>What can I use a steam mop for?</strong></h3>
<p><span style="font-size: small;">Steam mops are generally used to clean hard floors such as tiles. They can also be used to sanitise carpets and mattresses when fitted with the appropriate carpet attachment, which is included with most steam mops. You can have a look at Godfreys <a href="/steam-shampoo/steam-mops" target="_self">Steam Mops</a> here.&nbsp;</span></p>
<p><strong>&nbsp;</strong></p>
<h3><strong>Is it easy to shampoo my own carpets?</strong></h3>
<p><span style="font-size: small;">It is much easier to shampoo your own carpets than most people think. Hiring in a professional carpet cleaner can often be more costly than purchasing your own carpet shampooer! Take a look at Godfreys range of <a href="/steam-shampoo/carpet-shampooers" target="_self">Carpet Shampooers</a> here.</span></p>
<p>&nbsp;</p>
<h3><strong>Do you have vacuums for pet owners?</strong></h3>
<p><span style="font-size: small;">Godfreys have a wide range of vacuum cleaners for pet owners, often these include handy attachments for removing pet hair. View Godfreys range of <a href="/vacuum-cleaners/speciality/pet-hair-vacuum-cleaners" target="_self">pet vacuum cleaners</a> here.</span></p>
<p>&nbsp;</p>
<h3><strong>I suffer from asthma, which vacuums are best for me?</strong></h3>
<p><span style="font-size: small;">Godfreys sell several vacuum cleaners which have been accepted into the National Asthma Council's Sensitive Choice program, which recommends products based on their ability to manage asthma within the home. Godfreys sell several vacuum cleaners which have been accepted into the National Asthma Council's Sensitive Choice program, which recommends products based on their ability to manage asthma within the home. You can read more about the best vacuum cleaners for Asthma sufferers in our <a href="/vacuum-cleaners/speciality/asthma-vacuum-cleaners" target="_self">Asthma vacuum cleaners</a> section.</span></p>
<p>&nbsp;</p>
<h3><strong>I have allergies, are there any vacuums which can help with this?</strong></h3>
<p><span style="font-size: small;">There are a range of vacuum cleaners that are great for removing allergy-causing dust mites within the home. You can find more information including vacuum cleaners that can help manage allergies in our <a href="/vacuum-cleaners/speciality/anti-allergy-vaccum-cleaners" target="_self">Anti-Allergy vacuum cleaner</a> section.</span></p>
<p>&nbsp;</p>
<h3><strong>How does a ducted vacuum work?</strong></h3>
<p><span style="font-size: small;">A ducted vacuum cleaner system has a centrally-located vacuum unit (usually in the garage), which is connected to a series of hose sockets around the house. The user then connects a long vacuum hose to any one of the hose sockets around the house depending on where they want to clean, and uses the attached vacuum rod and floor tool to clean the floor. This way, the user does not have to drag or push a vacuum unit around the home, they just have to move around with the vacuum hose. To clean other areas, users simply disconnect the hose and attach it at a hose socket closer to the new location. Ducted vacuum units generally have large dust capacities, so you can vacuum for much longer without having to change the bag. You can see Godfreys range of <a href="/vacuum-cleaners/ducted-vacuum-cleaners" target="_self">ducted vacuums here</a>.</span></p>
<p><strong>&nbsp;</strong></p>
<h3><strong>How can I get my ducted vacuum installed?</strong></h3>
<p><span style="font-size: small;">There are several ways to get your ducted vacuum installed. Godfreys can offer a do-it-yourself kit complete with a helpful guidebook, we can arrange for a swap-out with an existing ducted system, or alternatively we can organise a new installation with a qualified installation company. For more information, please visit your local Godfreys or get in touch with Customer Service.</span></p>
<p>&nbsp;</p>
<h3><strong>How does a bagless vacuum cleaner work?</strong></h3>
<p><span style="font-size: small;">Bagless vacuum cleaners work by creating an 'air cyclone' in the centre of the canister. This causes dust and dirt particles to fly to the outside of the cyclone and then drop to the base of the canister. You can then easily empty the canister (usually with one push of a button) and continue to vacuum.&nbsp; For more information you can view our information page <a href="/floor-cleaning-tips/bagged-vs-bagless-vacuum-cleaners" target="_self">Bagged vs Bagless vacuum cleaners</a>.</span></p>
<p><strong>&nbsp;</strong></p>
<h3><strong>How are commercial vacuum cleaners different?</strong></h3>
<p><span style="font-size: small;">Commercial vacuums differ from domestic vacuums in several ways. They are generally built for daily use, whereas domestic vacuums are generally designed for weekly use. They are built of more durable materials as they have a tendency to get knocked around more, and they often have larger dust capacities. You can view Godfreys range of <a href="/vacuum-cleaners/commercial-vacuum-cleaners" target="_self">commercial vacuum cleaners here</a>.</span></p>
<p>&nbsp;</p>
<h3><strong>I'd like to work for Godfreys, who should I contact?</strong></h3>
<p><span style="font-size: small;">If you would like to work for Godfreys, then please consult our <a href="https://www.onetest.com.au/godfreyscareers/" target="_blank">Careers page</a> for current job opportunities.</span></p>
<p>&nbsp;</p>
<h3><strong>I have a problem with my vacuum and it's still under warranty. What should I do?</strong></h3>
<p><span style="font-size: small;">Take your vacuum cleaner along with proof of purchase to your local Godfreys store. To find your nearest store, use our <a href="/store-locator" target="_self">Store Locator</a>. If you purchased your vacuum online or cannot get to a store, please contact <a href="/contact-us" target="_self">Godfreys Customer Service</a>.</span></p>
EOD;

    $pageRootTemplate = 'two_columns_left';
    $pageLayoutUpdateXml = <<<EOD
EOD;


    $pageCustomLayoutUpdateXML = <<<EOD
EOD;

    $pageMetaKeywords = "";
    $pageMetaDescription = "";
    $page = Mage::getModel('cms/page')->load($pageIdentifier);
    if ($page->getId() == 0) {
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