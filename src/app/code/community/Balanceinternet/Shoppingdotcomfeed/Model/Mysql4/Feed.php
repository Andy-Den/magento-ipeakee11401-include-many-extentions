<?php

class Balanceinternet_Shoppingdotcomfeed_Model_Mysql4_Feed extends Mage_Core_Model_Mysql4_Abstract {

    public function _construct() {
        $this->_init('shoppingdotcomfeed/feed', 'id');
    }

    /**
     * Return id_store for feed given an id
     *
     * @param int $id_feed
     * @return int $id_store  
     */
    public function getStoreIdForFeed($id_feed) {

        $conn = Mage::getSingleton('core/resource')->getConnection('core_read');
        $sdcFeedResults = $conn->fetchAll("SELECT id_store FROM {$this->getTable('shoppingdotcomfeed/feed')} WHERE id = ?", array($id_feed));
        $id_store = $sdcFeedResults[0]['id_store'];
        return $id_store;
    }

    /**
     * Get the feed Content for a given feed
     *
     * @param int $idFeed
     * @param int $status
     * @return array $sdcFeed
     */
    public function getExportFeedContent($idFeed, $status) {
        $conn = Mage::getSingleton('core/resource')->getConnection('core_read');
        $sdcFeed = $conn->fetchAll("SELECT id, filename, ftp, username, password, status FROM {$this->getTable('shoppingdotcomfeed/feed')} WHERE id = ? and status = ?", array($idFeed, $status));
        return $sdcFeed;
    }

    /**
     * Update Feed Products Sent From Grid
     *
     * @param int $id_feed
     */
    public function updateFeedExportTime($id_feed) {
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $write->query("UPDATE {$this->getTable('shoppingdotcomfeed/feed')} SET successful_export = now() WHERE id = $id_feed");
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
    }

    /**
     * Set Product Feed Status (Success or write the error)
     *
     * @param boolean $status - 0 fail, 1 success
     * @param int $id_feed 
     * @param array $errors 
     * @param exception $e
     */
    public function setProductFeedSuccess($status, $id_feed, $errors) {
        $errorMessages = null;
        if (isset($errors)) {
            foreach ($errors as $error)
                $errorMessages .= $error;
        }
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        if ($status) {
            $write->query("UPDATE {$this->getTable('shoppingdotcomfeed/feed')} SET error = ? WHERE id = ?", array('None', $id_feed));
        } else {
            $write->query("UPDATE {$this->getTable('shoppingdotcomfeed/feed')} SET error = ? WHERE id = ?", array($errorMessages, $id_feed));
        }
    }

    /**
     * Update Successful Upload time of file
     *
     * @param int $id_feed
     */
    public function updateSuccessfulUpload($id_feed) {
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $write->query("UPDATE {$this->getTable('shoppingdotcomfeed/feed')} SET successful_upload = now() WHERE id = ?", array($id_feed));
    }

    /**
     * Update Successful Upload time of file
     *
     * @param int $id_feed
     */    
    public function getFileNameOnFeedSuccessPage($id_feed) {

        $conn = Mage::getSingleton('core/resource')->getConnection('core_read');
        $sdcFeedResults = $conn->fetchAll("SELECT filename FROM {$this->getTable('shoppingdotcomfeed/feed')} WHERE id = ?", array($id_feed));
        return $sdcFeedResults[0]['filename'];
    }

}
