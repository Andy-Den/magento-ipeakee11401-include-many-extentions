<?php

class Balance_NoStepCheckout_Helper_Data extends Mage_Core_Helper_Abstract {

    public function isEnabled() {
        return Mage::getStoreConfig('balance_nostepcheckout/general/enabled');
    }

    public function getConfigData($node) {
        return Mage::getStoreConfig('balance_nostepcheckout/' . $node);
    }

    public function validateCustomerAddress($customer) {
        if (!$customer->getDefaultBillingAddress() || !$customer->getDefaultShippingAddress()) {
            return false;
        }
        return true;
    }

    public function getAddressFields() {
        return array(
            'firstname', 'middlename', 'lastname', 'company', 'street', 'city', 'region_id', 'region', 'country_id', 'postcode', 'telephone', 'fax'
        );
    }

    public function formatSecureUrl($url) {
        $isSecure = Mage::app()->getStore()->isCurrentlySecure();
        return $isSecure ? str_replace('http:', 'https:', $url) : $url;
    }

}