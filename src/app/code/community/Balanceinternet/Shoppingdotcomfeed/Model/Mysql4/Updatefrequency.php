<?php

class Balanceinternet_Shoppingdotcomfeed_Model_Mysql4_Updatefrequency extends Mage_Core_Model_Mysql4_Abstract {

    public function _construct() {
        $this->_init('shoppingdotcomfeed/updatefrequency', 'id');
    }

    /**
     * Return all frequencies of export Daily, Weekly
     *
     * @return string $frequencies
     */
    public function getFrequency() {
        $frequencies = array();
        $count = 0;

        $conn = Mage::getSingleton('core/resource')->getConnection('core_read');
        $results = $conn->fetchAll("SELECT id, label, cron_code FROM {$this->getTable('shoppingdotcomfeed/updatefrequency')}");

        $frequencies[$results[-1]['id']] = '';
        foreach ($results as $result) {
            $frequencies[$results[$count]['id']] = $result['label']; //. ' - '; . $result['cron_code'];
            $count++;
        }
        return $frequencies;
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
     * Return frequencies for Manage Feed grid
     *
     * @param $rowIndex
     * @return string  
     */
    public function getFrequenciesForManageFeedGrid($rowIndex) {
        $idFeedportal = $rowIndex;
        $conn = Mage::getSingleton('core/resource')->getConnection('core_read');
        $sdcFeedResults = $conn->fetchAll("SELECT id, country_code, country FROM {$this->getTable('shoppingdotcomfeed/feedportal')} WHERE id = ?", array($idFeedportal));
        return $sdcFeedResults[0]['country'] . ' - ' . $sdcFeedResults[0]['country_code'];
    }

    /**
     * Return Stores
     *
     * @return array $storesArr
     */
    public function getCountries() {
        $countries = array();
        $count = 0;
        $conn = Mage::getSingleton('core/resource')->getConnection('core_read');
        $results = $conn->fetchAll("SELECT id, country_code, country FROM {$this->getTable('shoppingdotcomfeed/feedportal')}");

        $countries[$results[-1]['id']] = '';
        foreach ($results as $result) {
            $countries[$results[$count]['id']] = $result['country'] . ' - ' . $result['country_code'];
            $count++;
        }
        return $countries;
    }

}
