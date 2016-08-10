<?php

class Balanceinternet_Shoppingdotcomfeed_Block_Adminhtml_Shoppingdotcomfeed_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form(array(
                    'id' => 'edit_form',
                    'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
                    'method' => 'post',
                    'enctype' => 'multipart/form-data'
                        )
        );

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }

    protected function _afterToHtml($html) {
        return $html . '<h3 class="icon-head head-adminhtml-shoppingdotcomfeed">How to add a feed:</h3>
            <p>To create a feed to Shopping.com you first require an existing account to link to the country portal. Please select the relevant country from the following list to visit the shopping.com portal and create your account.  Once you have setup your account, please note your FTP details and enter them into the feed details below so that the products can be uploaded to shopping.com.</p>';
    }

}