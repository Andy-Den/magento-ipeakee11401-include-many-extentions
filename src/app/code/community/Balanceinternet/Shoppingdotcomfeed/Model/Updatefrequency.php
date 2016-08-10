<?php

class Balanceinternet_Shoppingdotcomfeed_Model_Updatefrequency extends Mage_Core_Model_Abstract {

    public function _construct() {
        $this->_init('shoppingdotcomfeed/updatefrequency');
    }

    /**
     * Return signuplinks
     *
     * @return string $link
     */
    public function getSignupLinks() {
        $links = null;
        $collection = Mage::getModel('shoppingdotcomfeed/feedportal')
                ->getCollection()
                ->addFieldToSelect(array('id', 'country_code', 'country', 'namejoin_url'));
        foreach ($collection as $result) {
            $links .= '<a ' . ' class="' . 'id_frequency-' . $result->getData('id') . ' sdc-links"href="' . $result->getData('namejoin_url') . '" target="_blank">' . $result->getData('country_code') . ': ' . $result->getData('country') . '</a>' . "\n";
        }
        return $links;
    }

    /**
     * Return Stores
     *
     * @return array $storesArr
     */
    public function getStores() {
        $stores = Mage::app()->getStores();
        $storesArr = array();
        $count = 0;


        $storesArr[''] = '';
        foreach ($stores as $storeId => $val) {
            $storeCode = Mage::app()->getStore($storeId)->getCode();
            $storeName = Mage::app()->getStore($storeId)->getName();
            $storeId = Mage::app()->getStore($storeId)->getId();
            $storesArr[Mage::app()->getStore($storeId)->getId()] = Mage::app()->getStore($storeId)->getName() . ' - ' . $storeCode = Mage::app()->getStore($storeId)->getCode();
            $count++;
        }
        return $storesArr;
    }
    
   

}
