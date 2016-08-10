<?php

class Balanceinternet_Shoppingdotcomfeed_Block_Adminhtml_Shoppingdotcomfeed_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {

        $form = new Varien_Data_Form();

        $this->setForm($form);
        $fieldset = $form->addFieldset('shoppingdotcomfeed_form', array('legend' => Mage::helper('shoppingdotcomfeed')->__('Item information')));

        $js = '<script type="text/javascript">';
        $js .= "
                // Hide all links with the same sdc-links class
                var links = '.sdc-links';
                $$(links).each(function (e) {
                    e.hide();
                });
                

                $$(links).each(Element.hide);

                var updateFeedPortalLink = function() {
                    // Hide all links with the same sdc-links class
                    var links = '.sdc-links';
                    $$(links).each(function (e) {
                        e.hide();
                    });

                    var id_freq = '.id_frequency-' + $('id_feedportal').value;
                    $$(id_freq).each(function(selector) {
                        selector.show();
                    });


                }
                $('id_feedportal').observe('change', updateFeedPortalLink);
        ";
        $js .= '</script>';

        $fieldset->addField('id_feedportal', 'select', array(
            'label' => Mage::helper('shoppingdotcomfeed')->__('Country'),
            'class' => 'required-entry',
            'required' => true,
            'values' => Mage::getModel('shoppingdotcomfeed/updatefrequency')->getResource()->getCountries(),
            'after_element_html' => Mage::getModel('shoppingdotcomfeed/updatefrequency')->getSignupLinks(),
            'name' => 'id_feedportal'
        ));

        $fieldset->addField('id_store', 'select', array(
            'label' => Mage::helper('shoppingdotcomfeed')->__('Choose site'),
            'class' => 'required-entry',
            'required' => true,
            'values' => Mage::getModel('shoppingdotcomfeed/updatefrequency')->getStores(),
            'after_element_html' => $js,
            'name' => 'id_store',
        ));

        $statuses = array(
            array(
                'value' => '',
                'label' => '',
            ),
            array(
                'value' => 1,
                'label' => Mage::helper('shoppingdotcomfeed')->__('Enabled'),
            ),
            array(
                'value' => 0,
                'label' => Mage::helper('shoppingdotcomfeed')->__('Disabled'),
                ));
        $statuses = array_reverse($statuses);
        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('shoppingdotcomfeed')->__('Status'),
            'required' => true,
            'name' => 'status',
            'values' => $statuses
        ));

        $fieldset->addField('ftp', 'text', array(
            'label' => Mage::helper('shoppingdotcomfeed')->__('FTP URL'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'ftp',
            'after_element_html' => '<br /><small>Enter the FTP URL.  For example ftp.shopping.com</small>',
        ));

        $fieldset->addField('username', 'text', array(
            'label' => Mage::helper('shoppingdotcomfeed')->__('Username'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'username',
        ));

        $fieldset->addField('password', 'text', array(
            'label' => Mage::helper('shoppingdotcomfeed')->__('Password'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'password',
        ));

        $fieldset->addField('id_frequency', 'select', array(
            'label' => Mage::helper('shoppingdotcomfeed')->__('Frequency data is pushed to shopping.com'),
            'class' => 'required-entry',
            'required' => true,
            'values' => Mage::getModel('shoppingdotcomfeed/updatefrequency')->getResource()->getFrequency(),
            'name' => 'id_frequency',
        ));


        if (Mage::getSingleton('adminhtml/session')->getShoppingdotcomfeedData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getShoppingdotcomfeedData());
            Mage::getSingleton('adminhtml/session')->setShoppingdotcomfeedData(null);
        } elseif (Mage::registry('shoppingdotcomfeed_data')) {
            $form->setValues(Mage::registry('shoppingdotcomfeed_data')->getData());
        }
        return parent::_prepareForm();
    }

}