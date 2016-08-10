<?php

class Balanceinternet_Shoppingdotcomfeed_Model_Feed extends Mage_Core_Model_Abstract {

    public function _construct() {
        $this->_init('shoppingdotcomfeed/feed');
    }
    
    /**
     * Delete Feed
     *
     * @param int $id_feed
     */
    public function deleteFeed($id_feed) {
        Mage::getModel('shoppingdotcomfeed/feed')
                ->setId($id_feed)
                ->delete();
    }
    

}
