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
    // 404 page not found ultimo Page | Godfreys
    //==========================================================================
    $pageTitle          = "New 404 page not found";
    $pageIdentifier     = "404-page";
    $pageStores         = array(0);
    $pageIsActive       = 1;
    $pageContentHeading = "";
    $pageContent        = <<<EOD
<!--Main content-->
<div class="page-title ">
<h1 class="no-gutter grid12-8">Sorry, we can't seem to find the page you're looking for!</h1>
</div>
<div class="std">
<div class="godfrey-content">
<div class="godfrey-wrap">
<div class="left-godfrey-content"><!--Static block--> {{block type="cms/block" block_id="reasons_404"}}</div>
<div class="right-godfrey-content"><img src="{{media url="wysiwyg/Godfreys.png"}}" alt="" /></div>
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
            <link>/</link>
        </crumbInfo>
    </action>
    <action method="addCrumb">
        <crumbName>Error</crumbName>
        <crumbInfo><label>Error 404</label>
            <title>Error 404</title>
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
    } else {
        // if exists then delete
        $page->delete();
        $page = Mage::getModel('cms/page');
    }

    $page->setTitle($pageTitle);
    $page->setIdentifier($pageIdentifier);
    $page->setStores($pageStores);
    $page->setIsActive($pageIsActive);
    $page->setContent($pageContent);
    $page->setContentHeading($pageContentHeading);
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