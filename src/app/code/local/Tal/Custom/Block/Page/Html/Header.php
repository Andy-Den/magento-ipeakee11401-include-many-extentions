<?php
class Tal_Custom_Block_Page_Html_Header extends Mage_Page_Block_Html_Header{
	public function getWelcome()
    {
        if (empty($this->_data['welcome'])) {
            if (Mage::isInstalled() && Mage::getSingleton('customer/session')->isLoggedIn()) {
                $this->_data['welcome'] = $this->__('Welcome %s', '<a href="'.$this->getBaseUrl().'customer/account/">'.$this->escapeHtml( Mage::getSingleton('customer/session')->getCustomer()->getFirstname()).'</a>');
            } else {
                $this->_data['welcome'] = Mage::getStoreConfig('design/header/welcome');
            }
        }

        return $this->_data['welcome'];
    }
}