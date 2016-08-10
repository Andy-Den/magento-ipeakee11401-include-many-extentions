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
    // Contact US Page | Godfreys
    //==========================================================================
    $pageTitle          = "Contact Us";
    $pageIdentifier     = "contact-us";
    $pageStores         = array(Mage::app()->getStore()->getStoreId());
    $pageIsActive       = 1;
    $pageContentHeading = "Contact";
    $pageContent        = <<<EOD
        <script type="text/javascript">// <![CDATA[
    function getParameterByName(name)
    {
        name = name.replace(/[\[]/, "\[").replace(/[\]]/, "\]");
        var regexS = "[\?&]" + name + "=([^&#]*)";
        var regex = new RegExp(regexS);
        var results = regex.exec(window.location.search);
        if(results == null)
            return "";
        else
            return decodeURIComponent(results[1].replace(/\+/g, " "));
    }


    jQuery(document).ready(function(){
        if (getParameterByName('product')) {
            jQuery('#Field16').val(getParameterByName('product'));
            jQuery('#Field14').val('New Product Enquiry');
        }
    })
// ]]></script>
<div class="contact-content">
<h2 class="legend">customer service enquiries</h2>
<p>Please fill out the form below with any questions, comments or feedback you have and we will contact you shortly.</p>
<form id="form-validate" class="wufoo topLabel page" action="https://godfreysvacuums.wufoo.com/forms/q7x3z9/#public" method="post" enctype="multipart/form-data">
<ul>
<li id="foli1" class="notranslate "><label id="title1" class="desc" for="Field1"> Name <span id="req_1" class="req">*</span> </label>
<div><input id="Field1" class="input-text required-entry" tabindex="1" type="text" name="Field1" maxlength="255" /></div>
</li>
<li id="foli8" class="notranslate"><label id="title8" class="desc" for="Field8"> Email Address <span id="req_8" class="req">*</span> </label>
<div><input id="Field8" class="input-text required-entry validate-email" tabindex="2" type="text" name="Field8" maxlength="255" /></div>
</li>
<li id="foli12" class="notranslate"><label id="title12" class="desc" for="Field12"> Phone Number <span id="req_12" class="req">*</span> </label>
<div><input id="Field12" class="input-text required-entry  validate-phoneLax" tabindex="3" type="tel" name="Field12" maxlength="255" /></div>
</li>
<li id="foli14" class="notranslate"><label id="title14" class="desc" for="Field14"> Type of Enquiry </label>
<div><select id="Field14" class="field select medium" name="Field14"> <option selected="selected" value="Warranties &amp; Repairs"> Warranties &amp; Repairs </option> <option value="New Product Enquiry"> New Product Enquiry </option> <option value="Store Feedback"> Store Feedback </option> <option value="Website Feedback"> Website Feedback </option> <option value="Other"> Other </option> </select></div>
</li>
<li id="foli16" class="notranslate"><label id="title16" class="desc" for="Field16"> Subject <span id="req_16" class="req">*</span> </label>
<div><input id="Field16" class="input-text required-entry" tabindex="5" type="text" name="Field16" maxlength="255" /></div>
</li>
<li id="foli18" class="notranslate"><label id="title18" class="desc " for="Field18"> Online Order Number (optional) </label>
<div><input id="Field18" class="input-text" tabindex="7" type="text" name="Field18" /></div>
</li>
<li id="foli9" class="notranslate"><label id="title9" class="desc" for="Field9"> Questions/Comments <span id="req_9" class="req">*</span> </label>
<div><textarea id="Field9" class="input-text required-entry" name="Field9"></textarea></div>
</li>
<li class="buttons ">
<div><input id="saveForm" class="btTxt submit" type="submit" name="saveForm" value="Submit" /></div>
</li>
<li style="display: none;"><label for="comment">Do Not Fill This Out</label> <textarea id="comment" name="comment" rows="1" cols="1"></textarea> <input id="idstamp" type="hidden" name="idstamp" value="VxQWQtd+NbsLug8019Is/PqWJtX4P7B4KTQ7H+OM3ls=" /></li>
</ul>
</form></div>
<div class="contact-download">
<div class="contact-infor">
<p>Sales &amp; Customer Service <br /> <span class="contact-phone">{{config path="general/store_information/phone"}}</span></p>
</div>
<div class="image-contact"><img src="{{media url="wysiwyg/Godfreys.png"}}" alt="" /></div>
</div>
<script type="text/javascript">// <![CDATA[
    var dataForm = new VarienForm('form-validate', true);
// ]]></script>
EOD;

    $pageRootTemplate = 'two_columns_left';
    $pageLayoutUpdateXml = <<<EOD
    <reference name="left">
      <block type="reports/product_viewed" after="hierarchy_menu" name="right.reports.product.viewed" template="reports/product_viewed.phtml" />
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
        ->addStoreFilter(2, $withAdmin = true)
        ->addFieldToFilter('identifier', $pageIdentifier)
        ->getFirstItem()
    ;
    if ($page->getId() == 0)
    {
        $page = Mage::getModel('cms/page');
    }
    else
    {
        //if exists then delete
        $page = Mage::getModel('cms/page')->load($page->getId());
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