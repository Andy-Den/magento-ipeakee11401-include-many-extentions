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
    // Vacuum Cleaners Landing | Godfreys
    //==========================================================================
    $blockTitle = "Vacuum Cleaners Landing";
    $blockIdentifier = "vacuum-cleaner-landing";
    $blockIsActive = 1;
    $blockStores = array(Mage::app()->getStore()->getStoreId());
    $blockContent = <<<EOD
<div class="vacuum-landing-header">
    <p class="vacuum-landing-intro">Godfreys are the vacuum and cleaning specialists - so we have a huge range of vacuum cleaners to choose from. Please select from the options below to find the best vacuum for you:</p>
</div>
<div id="vacuum-landing-subs">
    <ul>
        <li class="subs-list">
            <div class="reg-content"><img src="{{media url="wysiwyg/Bagless-Vacuums.jpg"}}" alt="Bagless Vacuum Cleaners" />
                <h2><a href="{{config path="web/unsecure/base_url"}}vacuum-cleaners/vacuum-cleaner-types/bagless"><span>Bagless</span>Vacuum Cleaners</a></h2>
            </div>
            <div class="hover">
				<a href="{{config path="web/unsecure/base_url"}}vacuum-cleaners/vacuum-cleaner-types/bagless">Bagless vacuums do not require vacuum bags, and often feature easy-empty canisters instead.</a>
				<a class="view-range" href="{{config path="web/unsecure/base_url"}}vacuum-cleaners/vacuum-cleaners/vacuum-cleaner-types/bagless">View Range</a>
			</div>
        </li>
        <li class="subs-list">
            <div class="reg-content"><img src="{{media url="wysiwyg/Robot-Vacuums.jpg"}}" alt="Robot Vacuum Cleaners"/>
                <h2><a href="{{config path="web/unsecure/base_url"}}vacuum-cleaner-types/robot-vacuums"><span>Robot</span>Vacuum Cleaners</a></h2>
            </div>
            <div class="hover"><a href="{{config path="web/unsecure/base_url"}}vacuum-cleaners/vacuum-cleaner-types/robot-vacuums">Robot vacuums are designed to automatically vacuum your floors while you relax!</a>
				 <a class="view-range" href="{{config path="web/unsecure/base_url"}}vacuum-cleaners/vacuum-cleaner-types/robot-vacuums">View Range</a>
			</div>
        </li>
        <li class="subs-list">
            <div class="reg-content"><img src="{{media url="wysiwyg/Bagged-Vacuums.jpg"}}" alt="Bagged Vacuum Cleaners"/>
                <h2><a href="{{config path="web/unsecure/base_url"}}vacuum-cleaners/vacuum-cleaner-types/bagged"><span>Bagged</span>Vacuum Cleaners</a></h2>
            </div>
            <div class="hover">
				<a href="{{config path="web/unsecure/base_url"}}vacuum-cleaners/vacuum-cleaner-types/bagged">Bagged vacuum cleaners use vacuum bags to hygienically seal dust and dirt inside.</a>
				<a class="view-range" href="{{config path="web/unsecure/base_url"}}vacuum-cleaners/vacuum-cleaner-types/bagged">View Range</a>
			</div>
        </li>
        <li class="subs-list">
            <div class="reg-content"><img src="{{media url="wysiwyg/Top-Rated-Vacuums.jpg"}}" alt="Top-Rated Vacuum Cleaners" />
                <h2><a href="{{config path="web/unsecure/base_url"}}vacuum-cleaners/vacuum-cleaner-types/top-rated-vacuums"><span>Top-Rated</span>Vacuum Cleaners</a></h2>
             </div>
            <div class="hover"><a href="{{config path="web/unsecure/base_url"}}vacuum-cleaners/vacuum-cleaner-types/top-rated-vacuums">View the vacuums that receive the best reviews from our existing customers.</a>
				<a class="view-range" href="{{config path="web/unsecure/base_url"}}vacuum-cleaners/vacuum-cleaner-types/top-rated-vacuums">View Range</a>
			</div>
        </li>
        <li class="last subs-list">
            <div class="reg-content"><img src="{{media url="wysiwyg/Upright-Vacuums.jpg"}}" alt="Upright Vacuum Cleaners" />
                <h2><a href="{{config path="web/unsecure/base_url"}}vacuum-cleaners/vacuum-cleaner-types/upright"><span>Upright</span>Vacuum Cleaners</a></h2>
            </div>
            <div class="hover"><a href="{{config path="web/unsecure/base_url"}}vacuum-cleaners/vacuum-cleaner-types/upright">Upright vacuums are designed to be lightweight, all-in-one cleaning machines.</a>
				<a class="view-range" href="{{config path="web/unsecure/base_url"}}vacuum-cleaners/vacuum-cleaner-types/upright">View Range</a>
			</div>
        </li>
        <li class="last subs-list">
            <div class="reg-content"><img src="{{media url="wysiwyg/Stick-Vacuums.jpg"}}" alt="Stick Vacuum Cleaners"/>
                <h2><a href="{{config path="web/unsecure/base_url"}}vacuum-cleaners/vacuum-cleaner-types/stick-vacuums"><span>Stick</span>Vacuum Cleaners</a></h2>
                </div>
            <div class="hover"><a href="{{config path="web/unsecure/base_url"}}vacuum-cleaners/vacuum-cleaner-types/stick-vacuums">Stick vacuums are often cordless, and some also include a detachable hand vacuum.</a>
				<a class="view-range" href="{{config path="web/unsecure/base_url"}}vacuum-cleaners/vacuum-cleaner-types/stick-vacuums">View Range</a>
			</div>
        </li>
        <li class="last subs-list">
            <div class="reg-content"><img src="{{media url="wysiwyg/Handheld-Vacuums.jpg"}}" alt="Handheld Vacuum Cleaners" />
                <h2><a href="{{config path="web/unsecure/base_url"}}vacuum-cleaners/vacuum-cleaner-types/handheld-vacs"><span>Handheld</span> Vacuum Cleaners</a></h2>
            </div>
            <div class="hover"><a href="{{config path="web/unsecure/base_url"}}vacuum-cleaners/vacuum-cleaner-types/handheld-vacs">Handheld vacuums are small, lightweight machines designed for quick cleans.</a>
				<a class="view-range" href="{{config path="web/unsecure/base_url"}}vacuum-cleaners/vacuum-cleaner-types/handheld-vacs">View Range</a>
			</div>
        </li>
        <li class="last subs-list">
            <div class="reg-content">
				<img src="{{media url="wysiwyg/Pet-Hair-Vacuums.jpg"}}" alt="Pet Hair Vacuum Cleaners" />
                <h2><a href="{{config path="web/unsecure/base_url"}}vacuum-cleaners/speciality-vacuum-cleaners/pet-hair"><span>Pet Hair</span>Vacuum Cleaners</a></h2>
            </div>
            <div class="hover"><a href="{{config path="web/unsecure/base_url"}}vacuum-cleaners/speciality-vacuum-cleaners/pet-hair">These vacuums are designed to effectively remove pet hair from floors and furniture.</a>
				<a class="view-range" href="{{config path="web/unsecure/base_url"}}vacuum-cleaners/speciality-vacuum-cleaners/pet-hair">View Range</a>
			</div>
        </li>
    </ul>
</div>
<div id="contents" class="vacuum-landing-products">{{widget type="tal_tabs/tabs" template="tal/tabs/page/home-product-tabs.phtml" block_ids="popular-vacuum-cleaners" }}</div>
<div class="static-block-category">
{{block type="cms/block" block_id="vacuum-cleaners-content"}}{{block type="cms/block" block_id="vacuum-cleaners-content-NZ"}}
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



    $installer->endSetup();
} catch (Excpetion $e) {
    Mage::logException($e);
    Mage::log("ERROR IN SETUP " . $e->getMessage());
}

