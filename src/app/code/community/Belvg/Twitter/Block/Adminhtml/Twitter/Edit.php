<?php
/**
 * BelVG LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 *
 /***************************************
 *         MAGENTO EDITION USAGE NOTICE *
 *****************************************/
 /* This package designed for Magento COMMUNITY edition
 * BelVG does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BelVG does not provide extension support in case of
 * incorrect edition usage.
 /***************************************
 *         DISCLAIMER   *
 *****************************************/
 /* Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future.
 *****************************************************
 * @category   Belvg
 * @package    Belvg_Twitterconnect
 * @copyright  Copyright (c) 2010 - 2011 BelVG LLC. (http://www.belvg.com)
 * @license    http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 */?>
<?php

class Belvg_Twitter_Block_Adminhtml_Twitter_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'twitter';
        $this->_controller = 'adminhtml_twitter';
        
        $this->_updateButton('save', 'label', Mage::helper('twitter')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('twitter')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('twitter_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'twitter_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'twitter_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('twitter_data') && Mage::registry('twitter_data')->getId() ) {
            return Mage::helper('twitter')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('twitter_data')->getTitle()));
        } else {
            return Mage::helper('twitter')->__('Add Item');
        }
    }
    protected function _toHtml(){
		return Mage::app()->getLayout()->createBlock('adminhtml/store_switcher')->toHtml().parent::_toHtml();
    }
}