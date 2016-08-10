<?php

class Balanceinternet_Shoppingdotcomfeed_Model_Mysql4_Feedproducts extends Mage_Core_Model_Mysql4_Abstract {

    public function _construct() {
        $this->_init('shoppingdotcomfeed/feedproducts', 'id');
    }

    /**
     * Return frequencies for Manage Feed grid
     *
     * @param int $id_feed
     * @return array $productIds  
     */
    public function getProductIdsForFeed($id_feed) {

        $conn = Mage::getSingleton('core/resource')->getConnection('core_read');
        $sdcFeedResults = $conn->fetchAll("SELECT product_ids FROM {$this->getTable('shoppingdotcomfeed/feedproducts')} WHERE id = ?", array($id_feed));
        $productIds = unserialize($sdcFeedResults[0]['product_ids']);
        $productIds = implode(',', $productIds);
        return $productIds;
    }

    /**
     * Get all the feed Content for a given frequency
     *
     * @param array $frequency
     * @return array $sdcFeedResults
     */
    public function getAllExportFeedsContent($frequency) {
        $conn = Mage::getSingleton('core/resource')->getConnection('core_read');
        $sdcFeedResults = $conn->fetchAll("SELECT id, product_ids, id_frequency, id_store, id_feed FROM {$this->getTable('shoppingdotcomfeed/feedproducts')} WHERE id_frequency = ?", array($frequency));
        return $sdcFeedResults;
    }

    /**
     * Update Feed Products Sent From Grid
     *
     * @param array $productIds
     * @param int $id_feed
     */
    public function updatedFeedProductsFromGrid($productIds, $id_feed) {
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $write->query("UPDATE {$this->getTable('shoppingdotcomfeed/feedproducts')} SET product_ids = ? WHERE id = ?", array(serialize($productIds), $id_feed));
    }

    /**
     * Update Feed Products Sent From Grid
     *
     * @param array $productIds
     * @param int $id_feed
     */
    public function insertFeedProductsFromGrid($productIds, $id_feed) {
        $timeStamp = Mage::getModel('core/date')->timestamp(time());
        $dateTimeStamp = date('m/d/y h:i:s', $timeStamp);
        $dateToday = date('Y-m-d', $timeStamp);
        $conn = Mage::getSingleton('core/resource')->getConnection('core_read');
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');

        $sdcFeedResults = $conn->fetchAll("SELECT id, id_feedportal, id_frequency, id_store FROM sdc_feed WHERE id = ?", array($id_feed));
        $write->query("INSERT INTO {$this->getTable('shoppingdotcomfeed/feedproducts')} (product_ids, id_frequency, id_feed, id_feedportal, updated_at, id_store) VALUES (?, ?, ?, ?, ?, ?)", array(serialize($productIds), $sdcFeedResults[0]['id_frequency'], $sdcFeedResults[0]['id'], $sdcFeedResults[0]['id_feedportal'], date('Y-m-d h:i:s', $timeStamp), $sdcFeedResults[0]['id_store'])); // 2012-07-15 08:27:55
    }
    
    /**
     * Check Products have been saved the decide if we want to insert or update 
     *
     * @return string $link
     */
    public function checkProductsSaved($id_feed) {
        $conn = Mage::getSingleton('core/resource')->getConnection('core_read');
        $sdcFeedProducts = $conn->fetchAll("SELECT id FROM sdc_feedproducts WHERE id = ?", array($id_feed));
        return $sdcFeedProducts;
    }    

}
