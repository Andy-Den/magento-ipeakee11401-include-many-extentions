<?php

class Balanceinternet_Shoppingdotcomfeed_Block_Adminhtml_Shoppingdotcomfeed_Catalog_Product_Select_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {

        $form = new Varien_Data_Form();

        $this->setForm($form);
        $fieldset = $form->addFieldset('shoppingdotcomfeed_form', array('legend' => Mage::helper('shoppingdotcomfeed')->__('Item information')));

        
        
        $fieldset->addField('id_portal', 'text', array(
            'label' => Mage::helper('shoppingdotcomfeed')->__('Country / Portal'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'id_portal',
        ));        
        
        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('shoppingdotcomfeed')->__('Status'),
            'name' => 'status',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('shoppingdotcomfeed')->__('Enabled'),
                ),
                array(
                    'value' => 0,
                    'label' => Mage::helper('shoppingdotcomfeed')->__('Disabled'),
                ),
            ),
        ));

        $fieldset->addField('ftp', 'text', array(
            'label' => Mage::helper('shoppingdotcomfeed')->__('FTP URL'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'ftp',
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




        if (Mage::getSingleton('adminhtml/session')->getShoppingdotcomfeedData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getShoppingdotcomfeedData());
            Mage::getSingleton('adminhtml/session')->setShoppingdotcomfeedData(null);
        } elseif (Mage::registry('shoppingdotcomfeed_data')) {
            $form->setValues(Mage::registry('shoppingdotcomfeed_data')->getData());
        }
        return parent::_prepareForm();
    }

}