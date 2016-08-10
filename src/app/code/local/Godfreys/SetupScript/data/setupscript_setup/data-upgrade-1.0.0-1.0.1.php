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
    // 404 reasons static Block | Godfreys
    //==========================================================================
    $blockTitle = "404_reasons";
    $blockIdentifier = "reasons_404";
    $blockStores = array(0);
    $blockIsActive = 1;
    $blockContent = <<<EOD
        <div class="left-wrap">
            <label> Reasons for this may be:</label>
            <ul class="ordered-list">
                <li>The product is currently not available</li>
                <li>The link to the page has been broken</li>
                <li>The page has been moved</li>
            </ul>
            <p>You can <a href="#"> go back to the previous page</a> you were on, or <a href="#"> return to the homepage</a></p>
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
    // 404 page not found ultimo Page | Godfreys
    //==========================================================================
    $pageTitle = "404 page not found ultimo";
    $pageIdentifier = "404-page";
    $pageStores = array(0);
    $pageIsActive = 1;
    $pageUnderVersionControl = 0;
    $pageContentHeading = "";
    $pageContent = <<<EOD
        <!--Main content-->
                <div class="page-title ">
                    <h1 class="no-gutter grid12-8">Sorry, we can't seem to find the page you're looking for!</h1>
                </div>
                <div class="std">
                    <div class="godfrey-content">
                        <div class="godfrey-wrap">
                            <div class="left-godfrey-content">
                                {{block type="cms/block" block_id="reasons_404"}}
                            </div>
                            <div class="right-godfrey-content">

                            <img alt="404 not found" src="/media/Godfreys.png">
                        </div>
                        </div>
                    </div>
                </div>
        <!--End main content-->
EOD;

    $pageRootTemplate = 'one_column';
    $pageLayoutUpdateXml = <<<EOD
    <reference name="breadcrumbs">
            <action method="addCrumb">
                <crumbName>Home</crumbName>
                <crumbInfo><label>Home</label>
                    <title>Home</title>
                    <link>/home</link>
                </crumbInfo>
            </action>
            <action method="addCrumb">
                <crumbName>Error</crumbName>
                <crumbInfo><label>Error 404</label>
                    <title>Error</title>
                    <link>/customer/account/</link>
                </crumbInfo></action>
    </reference>
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