<?php
/**
 * Customer register form block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Balance_Warranty_Block_Registration_Login_Register extends Mage_Customer_Block_Form_Register
{

    /**
     * Retrieve form posting url
     *
     * @return string
     */
    public function getPostActionUrl()
    {
        return Mage::helper('warranty/customer')->getRegisterPostUrl();
    }

    /**
     * Retrieve back url
     *
     * @return string
     */
    public function getBackUrl()
    {
        $url = $this->getData('back_url');
        if (is_null($url)) {
            $url = $this->helper('customer')->getLoginUrl();
        }
        return $url;
    }
}
