<?php
/**
 * Helper
 * 
 * @category   Balance
 * @package    Balance_Warranty
 * @author     Carey Sizer <carey@balanceinternet.com.au>
 */
class Balance_Warranty_Helper_Data extends Mage_Core_Helper_Data
{
    /**
     * Get registration url
     * @return type 
     */
    public function getRegistrationUrl()
    {
        return $this->_getUrl('warranty/registration');
    }
}