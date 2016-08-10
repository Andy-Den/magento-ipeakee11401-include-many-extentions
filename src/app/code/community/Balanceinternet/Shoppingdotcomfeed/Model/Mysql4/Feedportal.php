<?php

class Balanceinternet_Shoppingdotcomfeed_Model_Mysql4_Feedportal extends Mage_Core_Model_Mysql4_Abstract {

    public function _construct() {
        $this->_init('shoppingdotcomfeed/feedportal', 'id');
    }

    /**
     * Return countries for Manage Feed grid
     *
     * @param $rowIndex
     * @return string  
     */
    public function getCountriesForManageFeedGrid($rowIndex) {
        $idFeedportal = $rowIndex;
        $conn = Mage::getSingleton('core/resource')->getConnection('core_read');
        $sdcFeedResults = $conn->fetchAll("SELECT id, country_code, country FROM {$this->getTable('shoppingdotcomfeed/feedportal')} WHERE id = ?", array($idFeedportal));
        return $sdcFeedResults[0]['country'] . ' - ' . $sdcFeedResults[0]['country_code'];
    }

    /**
     * Return frequencies for Manage Feed grid
     *
     * @param $rowIndex
     * @return string  
     */
    public function getFrequenciesForManageFeedGrid($rowIndex) {
        $idFrequency = $rowIndex;
        $conn = Mage::getSingleton('core/resource')->getConnection('core_read');
        $sdcUpdateFrequency = $conn->fetchAll("SELECT label FROM {$this->getTable('shoppingdotcomfeed/updatefrequency')} WHERE id = ?", array($idFrequency));
        return $sdcUpdateFrequency[0]['label'];
    }

    /**
     * Feed Portal Link on the Success page after creating feed
     *
     * @param $id_feedportal
     * @return string $link  
     */    
    public function getFeedPortalLinkOnSuccessPage($id_feedportal) {

        $conn = Mage::getSingleton('core/resource')->getConnection('core_read');
        $sdcFeedResults = $conn->fetchAll("SELECT namejoin_join, country, namejoin_url FROM {$this->getTable('shoppingdotcomfeed/feedportal')} WHERE id = ?", array($id_feedportal));
        $link = '<a href="' . $sdcFeedResults[0]['namejoin_url'] . '" target="blank">' . $sdcFeedResults[0]['namejoin_join'] . ' ' . $sdcFeedResults[0]['country'] . '</a>';

        return $link;
    }

}
