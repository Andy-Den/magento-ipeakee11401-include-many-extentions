<?php

class Balanceinternet_Shoppingdotcomfeed_Block_Adminhtml_Shoppingdotcomfeed_Complete extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();
        $id = $this->getRequest()->getParam('id');

        $this->_objectId = 'id';
        $this->_blockGroup = 'shoppingdotcomfeed';
        $this->_controller = 'adminhtml_shoppingdotcomfeed';
        $this->_removeButton('reset');
        $this->_removeButton('save');
    }

    public function getHeaderText() {
        if (Mage::registry('shoppingdotcomfeed_data') && Mage::registry('shoppingdotcomfeed_data')->getId()) {
            return Mage::helper('shoppingdotcomfeed')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('shoppingdotcomfeed_data')->getName()));
        } else {
            return Mage::helper('shoppingdotcomfeed')->__('Feed Completion');
        }
    }

    protected function _afterToHtml($html) {
        //$filename = Mage::helper('shoppingdotcomfeed')->getFileNameForFeed($this->getRequest()->getParam('id_feed')); // getFileNameOnFeedSuccessPage
        $filename = Mage::getModel('shoppingdotcomfeed/feed')->getResource()->getFileNameOnFeedSuccessPage($this->getRequest()->getParam('id_feed'));
        $feedPortalLink = Mage::getModel('shoppingdotcomfeed/feedportal')->getResource()->getFeedPortalLinkOnSuccessPage($this->getRequest()->getParam('id_feed')); //Mage::helper('shoppingdotcomfeed')->getFeedPortalLink($this->getRequest()->getParam('id_feed'));
        $html = null;
        return $html . '
            <style>
                .box-shadow {
                    border-radius: 5px 5px 5px 5px;
                    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.25);
                    margin-bottom: 15px;
                    padding: 15px;
                }
            </style>
            <h3 class="icon-head head-adminhtml-shoppingdotcomfeed">Congratulations!</h3>
            <p>Your datafeed has been created.<br />
               Please allow up to 4 hours to send your datafeed to the eBay Commerce Network.<br />
               You may check the status of the datafeed transfer at any time by visiting XXXXXXXXXXXX.
             </p><br />
             
            <div class="box-shadow">
                <p><b>IMPORTANT:</b><br />
                   Once Magento has successfully transferred your datafeed, please follow <a target=“_blank” href="http://merchantsupport.shopping.com/Magento_Integration">these steps</a> to complete setup. Your listings will not appear across the eBay Commerce Network until setup has been complete. 
                </p>
            </div>
            <p >For technical support you may <a target=“_blank” href="http://merchantsupport.shopping.com/Magento_Contact_Support">contact</a> eBay Commerce Network Merchant Support</p><br />
            <h4>filename: ' . $filename . '<br /></p>
            ';
    }

}