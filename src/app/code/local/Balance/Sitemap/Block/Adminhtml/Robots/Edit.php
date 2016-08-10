<?php

class Balance_Sitemap_Block_Adminhtml_Robots_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'robots_id';
        $this->_blockGroup = 'balance_sitemap';
        $this->_controller = 'adminhtml_robots';

        parent::__construct();

        $this->_updateButton('save', 'label', Mage::helper('balance_sitemap')->__('Save Robots.txt'));
        $this->_updateButton('delete', 'label', Mage::helper('balance_sitemap')->__('Delete Robots.txt'));

        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save and Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "            

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    /**
     * Get edit form container header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('balance_sitemap_robots')->getId()) {
            return Mage::helper('balance_sitemap')->__("Edit Robots.txt '%s'", $this->htmlEscape(Mage::registry('balance_sitemap_robots')->getTitle()));
        }
        else {
            return Mage::helper('balance_sitemap')->__('New Robots.txt');
        }
    }

}
