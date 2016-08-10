<?php

class Balanceinternet_Shoppingdotcomfeed_Block_Adminhtml_Shoppingdotcomfeed_Edit_Help extends Mage_Core_Block_Template {

    protected function _toHtml() {
        $html .= '<p>To create a feed to Shopping.com you first require an existing account to link to the country portal. Please select the relevant country from the following list to visit the shopping.com portal and create your account.  Once you have setup your account, please note your FTP details and enter them into the feed details below so that the products can be uploaded to shopping.com.  For more information, help & assistance on the 3 steps required, please click <a href="http://merchantsupport.shopping.com/Magento_Integration" target="_blank">here</a>';
        return $html;
    }

}