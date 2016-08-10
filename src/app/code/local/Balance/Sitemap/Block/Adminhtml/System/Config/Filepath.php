<?php

class Balance_Sitemap_Block_Adminhtml_System_Config_Filepath extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /*
     * Set template
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('balance/sitemap/system/config/filepath.phtml');
    }

    /**
     * Return element html
     *
     * @param  Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        //var_dump($element);
        return parent::_getElementHtml($element).''.$this->_toHtml();
    }

    /**
     * Return ajax url for button
     *
     * @return string
     */
    public function getAjaxCheckUrl()
    {
        return Mage::helper('adminhtml')->getUrl('balance_sitemap_admin/adminhtml_index/filepathcheck');
    }

    /**
     * Generate button html
     *
     * @return string
     */
    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
            'id'        => 'balance_sitemap_filepathcheck_button',
            'label'     => $this->helper('adminhtml')->__('Check'),
            'onclick'   => 'javascript:check(); return false;'
        ));

        return $button->toHtml();
    }
}