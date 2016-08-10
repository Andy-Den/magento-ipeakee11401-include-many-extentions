<?php

/**
 * Block to render customer's gender attribute
 *
 * @category   Balance
 * @package    Balance_Warranty
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Balance_Warranty_Block_Registration_Widget_Gender extends Mage_Customer_Block_Widget_Abstract
{
    /**
     * Initialize block
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('warranty/registration/widget/gender.phtml');
    }

    /**
     * Check if gender attribute enabled in system
     *
     * @return bool
     */
    public function isEnabled()
    {
        return true;
    }

    /**
     * Check if gender attribute marked as required
     *
     * @return bool
     */
    public function isRequired()
    {
        return true;
    }

    /**
     * Get current customer from session
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        return Mage::getSingleton('customer/session')->getCustomer();
    }
    
    /**
     * Get the id to use for the gender field
     * @param string $field
     * @return string 
     */
    public function getFieldId($field)
    {
        if($field == 'gender'){
            return 'contact:gender';
        }else{
            return parent::getFieldId($field);
        }
    }
    
    /**
     * Get the name to use for the gender field
     * @param string $field
     * @return string 
     */
    public function getFieldName($field)
    {
        if($field == 'gender'){
            return 'contact[gender]';
        }else{
            return parent::getFieldName($field);
        }
    }
}
